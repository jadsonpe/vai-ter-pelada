import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('[data-like-form]');

    if (! form) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector('[data-like-button]');
    const postId = form.dataset.postId;

    if (! button || ! postId || button.disabled) {
        return;
    }

    button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        });

        if (! response.ok) {
            form.submit();
            return;
        }

        const data = await response.json();
        const likedClasses = (button.dataset.likedClasses || '').split(' ').filter(Boolean);
        const unlikedClasses = (button.dataset.unlikedClasses || '').split(' ').filter(Boolean);
        const allStateClasses = [...likedClasses, ...unlikedClasses];

        document.querySelectorAll(`[data-like-form][data-post-id="${postId}"]`).forEach((likeForm) => {
            const likeButton = likeForm.querySelector('[data-like-button]');

            if (! likeButton) {
                return;
            }

            likeButton.classList.remove(...allStateClasses);
            likeButton.classList.add(...(data.liked ? likedClasses : unlikedClasses));
            likeButton.setAttribute('aria-pressed', data.liked ? 'true' : 'false');
            likeButton.setAttribute('aria-label', data.liked ? 'Remover curtida' : 'Curtir publicação');

            const icon = likeButton.querySelector('svg');
            if (icon) {
                icon.setAttribute('fill', data.liked ? 'currentColor' : 'none');
            }
        });

        document.querySelectorAll(`[data-like-count="${postId}"]`).forEach((counter) => {
            counter.textContent = counter.textContent.includes('curtida')
                ? data.likes_label
                : data.likes_count;
        });
    } catch (error) {
        form.submit();
    } finally {
        button.disabled = false;
    }
});

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-share-post], [data-share-page]');

    if (! button) {
        return;
    }

    const url = button.dataset.shareUrl;
    const title = button.dataset.shareTitle || document.title;
    const text = button.dataset.shareText || 'Olha esta publicação no Vai Ter Pelada.';

    if (! url) {
        return;
    }

    if (navigator.share) {
        event.preventDefault();

        try {
            await navigator.share({ title, text, url });
            return;
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }
        }
    } else if (button.matches('a[href]')) {
        return;
    }

    event.preventDefault();

    const message = `${text} ${url}`.trim();
    const whatsappUrl = button.getAttribute('href') || `https://wa.me/?text=${encodeURIComponent(message)}`;
    const opened = window.open(whatsappUrl, '_blank', 'noopener,noreferrer');

    if (! opened && navigator.clipboard) {
        await navigator.clipboard.writeText(url);
    }
});

document.querySelectorAll('[data-stories-root]').forEach((root) => {
    const dataNode = root.querySelector('[data-stories-json]');
    const viewer = root.querySelector('[data-story-viewer]');

    if (! dataNode || ! viewer) {
        return;
    }

    let groups = [];

    try {
        groups = JSON.parse(dataNode.textContent || '[]');
    } catch (error) {
        groups = [];
    }

    if (! groups.length) {
        return;
    }

    const image = viewer.querySelector('[data-story-image]');
    const video = viewer.querySelector('[data-story-video]');
    const closeButton = viewer.querySelector('[data-story-close]');
    const prevButton = viewer.querySelector('[data-story-prev]');
    const nextButton = viewer.querySelector('[data-story-next]');
    const progress = viewer.querySelector('[data-story-progress]');
    const avatar = viewer.querySelector('[data-story-avatar]');
    const name = viewer.querySelector('[data-story-name]');
    const time = viewer.querySelector('[data-story-time]');
    const caption = viewer.querySelector('[data-story-caption]');
    const profileLink = viewer.querySelector('[data-story-profile]');
    const reportForm = viewer.querySelector('[data-story-report-form]');
    const deleteForm = viewer.querySelector('[data-story-delete-form]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const state = {
        group: 0,
        item: 0,
    };

    const currentGroup = () => groups[state.group];
    const currentItem = () => currentGroup()?.items?.[state.item];

    const markViewed = async (item) => {
        if (! item?.view_url) {
            return;
        }

        try {
            await fetch(item.view_url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                },
            });
        } catch (error) {
            // Visualizacao e informativa; falha de rede nao deve travar o story.
        }
    };

    const close = () => {
        viewer.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        video.pause();
        video.removeAttribute('src');
    };

    const renderProgress = () => {
        progress.innerHTML = '';
        const items = currentGroup()?.items || [];

        items.forEach((item, index) => {
            const bar = document.createElement('span');
            bar.className = `h-1 flex-1 rounded-full ${index <= state.item ? 'bg-white' : 'bg-white/25'}`;
            progress.appendChild(bar);
        });
    };

    const renderAvatar = (group) => {
        avatar.innerHTML = '';

        if (group.avatar) {
            const img = document.createElement('img');
            img.src = group.avatar;
            img.alt = group.name;
            img.className = 'h-full w-full object-cover';
            avatar.appendChild(img);
            return;
        }

        avatar.textContent = group.initials || 'P';
    };

    const render = () => {
        const group = currentGroup();
        const item = currentItem();

        if (! group || ! item) {
            close();
            return;
        }

        renderProgress();
        renderAvatar(group);
        name.textContent = group.name || 'Peladeiro';
        time.textContent = item.published_at || '';
        caption.textContent = item.caption || '';
        profileLink.href = group.profile_url || '#';

        reportForm.classList.toggle('hidden', ! item.report_url);
        reportForm.action = item.report_url || '#';
        deleteForm.classList.toggle('hidden', ! item.delete_url);
        deleteForm.action = item.delete_url || '#';

        image.classList.add('hidden');
        video.classList.add('hidden');
        video.pause();
        video.removeAttribute('src');

        if (item.type === 'video') {
            video.src = item.media_url;
            video.classList.remove('hidden');
            video.play().catch(() => {});
        } else {
            image.src = item.media_url;
            image.classList.remove('hidden');
        }

        markViewed(item);
    };

    const open = (groupIndex) => {
        state.group = Number(groupIndex) || 0;
        state.item = 0;
        viewer.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        render();
    };

    const next = () => {
        const group = currentGroup();

        if (group && state.item < group.items.length - 1) {
            state.item += 1;
            render();
            return;
        }

        if (state.group < groups.length - 1) {
            state.group += 1;
            state.item = 0;
            render();
            return;
        }

        close();
    };

    const prev = () => {
        if (state.item > 0) {
            state.item -= 1;
            render();
            return;
        }

        if (state.group > 0) {
            state.group -= 1;
            state.item = Math.max(0, (currentGroup()?.items?.length || 1) - 1);
            render();
        }
    };

    root.querySelectorAll('[data-story-open]').forEach((button) => {
        button.addEventListener('click', () => open(button.dataset.storyOpen));
    });

    closeButton?.addEventListener('click', close);
    nextButton?.addEventListener('click', next);
    prevButton?.addEventListener('click', prev);
    video?.addEventListener('ended', next);

    document.addEventListener('keydown', (event) => {
        if (viewer.classList.contains('hidden')) {
            return;
        }

        if (event.key === 'Escape') {
            close();
        } else if (event.key === 'ArrowRight') {
            next();
        } else if (event.key === 'ArrowLeft') {
            prev();
        }
    });
});
