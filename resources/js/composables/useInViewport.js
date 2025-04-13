import { onMounted, onUnmounted, ref } from 'vue';

export function useInViewport() {
    const target = ref(null);
    const isVisible = ref(false);

    let observer;

    const callback = ([entry]) => {
        if (entry.isIntersecting) {
            isVisible.value = true;
            observer.disconnect(); // Stop observing after visible (optional)
        }
    };

    onMounted(() => {
        observer = new IntersectionObserver(callback, {
            threshold: 0.1,
        });

        if (target.value) {
            observer.observe(target.value);
        }
    });

    onUnmounted(() => {
        if (observer && target.value) {
            observer.unobserve(target.value);
        }
    });

    return { target, isVisible };
}
