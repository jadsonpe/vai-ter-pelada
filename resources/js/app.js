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
    const button = event.target.closest('[data-share-post]');

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
        try {
            await navigator.share({ title, text, url });
            return;
        } catch (error) {
            if (error?.name === 'AbortError') {
                return;
            }
        }
    }

    const message = `${text} ${url}`.trim();
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    const opened = window.open(whatsappUrl, '_blank', 'noopener,noreferrer');

    if (! opened && navigator.clipboard) {
        await navigator.clipboard.writeText(url);
    }
});
