import { onMounted, onUnmounted, ref } from 'vue';

export function useDarkMode() {
    const isDark = ref(false);

    let observer: MutationObserver | null = null;

    const update = () => {
        isDark.value = document.documentElement.classList.contains('dark');
    };

    onMounted(() => {
        update();

        observer = new MutationObserver(update);
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });
    });

    onUnmounted(() => {
        observer?.disconnect();
        observer = null;
    });

    return {
        isDark,
    };
}
