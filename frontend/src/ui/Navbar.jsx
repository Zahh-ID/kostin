import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { FiMenu, FiX } from 'react-icons/fi';
import './Navbar.css';

const Navbar = ({ brand, links, activeHref, actions }) => {
    const [isScrolled, setIsScrolled] = useState(false);
    const [isMobileOpen, setIsMobileOpen] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setIsScrolled(window.scrollY > 20);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const isExternal = (href) => href.startsWith('http');

    return (
        <>
            <nav className={`navbar ${isScrolled ? 'scrolled' : ''}`}>
                <div className="navbar-left">
                    {brand}
                </div>

                <div className="navbar-center">
                    <ul className="nav-links">
                        {links.map((link) => {
                            const isActive = activeHref === link.href;
                            const LinkComp = isExternal(link.href) ? 'a' : Link;
                            const props = isExternal(link.href) ? { href: link.href } : { to: link.href };

                            return (
                                <li key={link.href}>
                                    <LinkComp
                                        {...props}
                                        className={`nav-link ${isActive ? 'active' : ''}`}
                                    >
                                        {link.label}
                                    </LinkComp>
                                </li>
                            );
                        })}
                    </ul>
                </div>

                <div className="navbar-right">
                    {actions}
                </div>

                <button
                    className="mobile-toggle"
                    onClick={() => setIsMobileOpen(!isMobileOpen)}
                >
                    {isMobileOpen ? <FiX /> : <FiMenu />}
                </button>
            </nav>

            <div className={`mobile-menu ${isMobileOpen ? 'open' : ''}`}>
                <div className="mobile-nav-links">
                    {links.map((link) => {
                        const isActive = activeHref === link.href;
                        const LinkComp = isExternal(link.href) ? 'a' : Link;
                        const props = isExternal(link.href) ? { href: link.href } : { to: link.href };

                        return (
                            <LinkComp
                                key={link.href}
                                {...props}
                                className={`mobile-nav-link ${isActive ? 'active' : ''}`}
                                onClick={() => setIsMobileOpen(false)}
                            >
                                {link.label}
                            </LinkComp>
                        );
                    })}
                </div>
                <div className="mobile-actions">
                    {actions}
                </div>
            </div>
        </>
    );
};

export default Navbar;
