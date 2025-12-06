import React, { useRef, useEffect } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const AnimatedText = ({ text, className = '', delay = 0 }) => {
    const containerRef = useRef(null);
    const words = text.split(' ');

    useEffect(() => {
        const ctx = gsap.context(() => {
            const chars = containerRef.current.querySelectorAll('.char');

            gsap.fromTo(
                chars,
                {
                    opacity: 0,
                    y: 50,
                    filter: 'blur(10px)',
                    rotateX: -45
                },
                {
                    opacity: 1,
                    y: 0,
                    filter: 'blur(0px)',
                    rotateX: 0,
                    duration: 1,
                    stagger: 0.05,
                    ease: 'power4.out',
                    delay: delay,
                    scrollTrigger: {
                        trigger: containerRef.current,
                        start: "top 85%",
                        toggleActions: "play none none reverse"
                    }
                }
            );
        }, containerRef);

        return () => ctx.revert();
    }, [text, delay]);

    return (
        <div ref={containerRef} className={`perspective-1000 ${className}`}>
            {words.map((word, wordIndex) => (
                <span key={wordIndex} className="inline-block whitespace-nowrap mr-[0.25em]">
                    {word.split('').map((char, charIndex) => (
                        <span key={`${wordIndex}-${charIndex}`} className="char inline-block transform-style-3d">
                            {char}
                        </span>
                    ))}
                </span>
            ))}
        </div>
    );
};

export default AnimatedText;
