const selector = "[data-scroll-reveal]";
const viewportInset = 48;

export function observeScrollReveal(root: HTMLElement): () => void {
  if (
    !window.IntersectionObserver ||
    !window.MutationObserver ||
    !window.matchMedia ||
    !root.animate
  ) {
    return () => {};
  }

  const motion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const tracked = new Set<HTMLElement>();
  const pending = new Map<HTMLElement, Animation>();
  const animations = new Set<Animation>();
  let frame: number | undefined;

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) reveal(entry.target as HTMLElement);
      }
    },
    { rootMargin: `0px 0px -${viewportInset}px 0px`, threshold: 0 },
  );

  function reveal(element: HTMLElement, instantly = false): void {
    const animation = pending.get(element);
    if (!animation) return;
    if (instantly) {
      animation.cancel();
      animations.delete(animation);
    } else animation.play();
    pending.delete(element);
    observer.unobserve(element);
  }

  function register(): void {
    for (const element of tracked) {
      if (!root.contains(element)) {
        observer.unobserve(element);
        const animation = pending.get(element);
        if (animation) {
          animation.cancel();
          animations.delete(animation);
        }
        pending.delete(element);
        tracked.delete(element);
      }
    }

    const elements = [...root.querySelectorAll<HTMLElement>(selector)].filter(
      (element) => !tracked.has(element),
    );
    // Measure before changing styles, and keep initially visible content readable.
    const belowViewport = elements.filter(
      (element) =>
        !motion.matches &&
        !element.contains(document.activeElement) &&
        element.getBoundingClientRect().top >=
          window.innerHeight - viewportInset,
    );
    for (const element of elements) tracked.add(element);
    for (const element of belowViewport) {
      // Web Animations keeps state outside HTML, which may still be hydrating.
      const animation = element.animate(
        [
          { opacity: 0, transform: "translateY(28px)" },
          { opacity: 1, transform: "none" },
        ],
        {
          duration: 650,
          delay: Number(element.dataset.scrollDelay || 0) * 80,
          easing: "cubic-bezier(0.22, 1, 0.36, 1)",
          fill: "backwards",
        },
      );
      animations.add(animation);
      animation.onfinish = () => animations.delete(animation);
      animation.pause();
      pending.set(element, animation);
      observer.observe(element);
    }
  }

  function scheduleRegistration(): void {
    if (frame !== undefined) return;
    frame = window.requestAnimationFrame(() => {
      frame = undefined;
      register();
    });
  }

  function revealFocusedContent(event: FocusEvent): void {
    if (!(event.target instanceof Element)) return;
    for (const element of pending.keys()) {
      if (element.contains(event.target)) reveal(element, true);
    }
  }

  function handleMotionChange(): void {
    if (motion.matches) {
      for (const element of pending.keys()) reveal(element, true);
      for (const animation of animations) animation.cancel();
      animations.clear();
    }
  }

  const mutations = new MutationObserver(scheduleRegistration);
  mutations.observe(root, { childList: true, subtree: true });
  root.addEventListener("focusin", revealFocusedContent);
  motion.addEventListener("change", handleMotionChange);
  register();

  return () => {
    observer.disconnect();
    mutations.disconnect();
    root.removeEventListener("focusin", revealFocusedContent);
    motion.removeEventListener("change", handleMotionChange);
    if (frame !== undefined) window.cancelAnimationFrame(frame);
    for (const animation of animations) animation.cancel();
  };
}
