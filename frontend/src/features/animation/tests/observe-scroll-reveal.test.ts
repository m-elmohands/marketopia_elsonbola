import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";
import { observeScrollReveal } from "../services/observe-scroll-reveal";

class RevealElement {
  dataset: Record<string, string> = {};
  children = new Set<unknown>();
  animation = {
    pause: vi.fn(),
    play: vi.fn(),
    cancel: vi.fn(),
    onfinish: null,
  };
  animate = vi.fn(() => this.animation);
  constructor(public top: number) {}
  getBoundingClientRect() {
    return { top: this.top };
  }
  contains(element: unknown) {
    return element === this || this.children.has(element);
  }
}
class TestIntersectionObserver {
  static instance: TestIntersectionObserver;
  observe = vi.fn();
  unobserve = vi.fn();
  disconnect = vi.fn();
  constructor(
    public callback: IntersectionObserverCallback,
    public options: IntersectionObserverInit,
  ) {
    TestIntersectionObserver.instance = this;
  }
  enter(element: RevealElement) {
    this.callback(
      [
        {
          target: element,
          isIntersecting: true,
        } as unknown as IntersectionObserverEntry,
      ],
      this as unknown as IntersectionObserver,
    );
  }
}
class TestMutationObserver {
  static instance: TestMutationObserver;
  observe = vi.fn();
  disconnect = vi.fn();
  constructor(public callback: MutationCallback) {
    TestMutationObserver.instance = this;
  }
  update() {
    this.callback([], this as unknown as MutationObserver);
  }
}
function setup() {
  const initial = new RevealElement(100),
    below = new RevealElement(950);
  const elements = [initial, below],
    listeners = new Map<string, EventListener>();
  const motion = {
    matches: false,
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
  };
  const frames: FrameRequestCallback[] = [];
  const root = {
    animate: vi.fn(),
    querySelectorAll: () => elements,
    contains: (element: RevealElement) => elements.includes(element),
    addEventListener: (name: string, listener: EventListener) =>
      listeners.set(name, listener),
    removeEventListener: (name: string) => listeners.delete(name),
  };
  const browser = {
    IntersectionObserver: TestIntersectionObserver,
    MutationObserver: TestMutationObserver,
    innerHeight: 800,
    matchMedia: vi.fn(() => motion),
    requestAnimationFrame: vi.fn((callback: FrameRequestCallback) => {
      frames.push(callback);
      return frames.length;
    }),
    cancelAnimationFrame: vi.fn(),
  };
  vi.stubGlobal("window", browser);
  vi.stubGlobal("document", { activeElement: null });
  return {
    root: root as unknown as HTMLElement,
    elements,
    initial,
    below,
    listeners,
    motion,
    browser,
    frames,
  };
}
beforeEach(() => {
  vi.stubGlobal("Element", RevealElement);
  vi.stubGlobal("IntersectionObserver", TestIntersectionObserver);
  vi.stubGlobal("MutationObserver", TestMutationObserver);
});
afterEach(() => vi.unstubAllGlobals());

describe("scroll reveal", () => {
  it("keeps first-screen content readable and observes only below-screen content", () => {
    const { root, initial, below } = setup();
    observeScrollReveal(root);
    expect(initial.dataset.revealState).toBeUndefined();
    expect(below.animation.pause).toHaveBeenCalledTimes(1);
    expect(below.dataset).toEqual({});
    expect(
      TestIntersectionObserver.instance.observe,
    ).toHaveBeenCalledExactlyOnceWith(below);
  });
  it("reveals entering content once and releases its observer", () => {
    const { root, below } = setup();
    observeScrollReveal(root);
    const observer = TestIntersectionObserver.instance;
    observer.enter(below);
    observer.enter(below);
    expect(below.animation.play).toHaveBeenCalledTimes(1);
    expect(observer.unobserve).toHaveBeenCalledExactlyOnceWith(below);
  });
  it("does not hide content when reduced motion is enabled", () => {
    const { root, below, motion } = setup();
    motion.matches = true;
    observeScrollReveal(root);
    expect(below.dataset.revealState).toBeUndefined();
    expect(TestIntersectionObserver.instance.observe).not.toHaveBeenCalled();
  });
  it("reveals pending content immediately when reduced motion changes", () => {
    const { root, below, motion } = setup();
    observeScrollReveal(root);
    motion.matches = true;
    motion.addEventListener.mock.calls[0][1]();
    expect(below.animation.cancel).toHaveBeenCalledTimes(1);
  });
  it("keeps focused links visible without waiting for the animation", () => {
    const { root, below, listeners } = setup();
    const link = new RevealElement(1000);
    below.children.add(link);
    observeScrollReveal(root);
    listeners.get("focusin")?.({ target: link } as unknown as Event);
    expect(below.animation.cancel).toHaveBeenCalledTimes(1);
  });
  it("registers streamed content and batches repeated DOM updates", () => {
    const { root, elements, browser, frames } = setup();
    observeScrollReveal(root);
    const streamed = new RevealElement(1500);
    elements.push(streamed);
    TestMutationObserver.instance.update();
    TestMutationObserver.instance.update();
    expect(browser.requestAnimationFrame).toHaveBeenCalledTimes(1);
    frames[0](0);
    expect(streamed.animation.pause).toHaveBeenCalledTimes(1);
    expect(TestIntersectionObserver.instance.observe).toHaveBeenCalledWith(
      streamed,
    );
  });
  it("releases removed catalog cards", () => {
    const { root, elements, below, frames } = setup();
    observeScrollReveal(root);
    elements.splice(elements.indexOf(below), 1);
    TestMutationObserver.instance.update();
    frames[0](0);
    expect(TestIntersectionObserver.instance.unobserve).toHaveBeenCalledWith(
      below,
    );
  });
  it("cleans pending styles, observers, listeners, and scheduled work on navigation", () => {
    const { root, below, listeners, browser, motion } = setup();
    const stop = observeScrollReveal(root);
    TestMutationObserver.instance.update();
    stop();
    expect(below.animation.cancel).toHaveBeenCalledTimes(1);
    expect(listeners.size).toBe(0);
    expect(TestIntersectionObserver.instance.disconnect).toHaveBeenCalledTimes(
      1,
    );
    expect(TestMutationObserver.instance.disconnect).toHaveBeenCalledTimes(1);
    expect(motion.removeEventListener).toHaveBeenCalledTimes(1);
    expect(browser.cancelAnimationFrame).toHaveBeenCalledWith(1);
  });
  it("leaves unsupported browsers readable", () => {
    const { root, below, browser } = setup();
    Object.assign(browser, { IntersectionObserver: undefined });
    observeScrollReveal(root)();
    expect(below.dataset.revealState).toBeUndefined();
  });
});
