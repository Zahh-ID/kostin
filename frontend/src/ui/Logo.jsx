import React from 'react';

export const Logo = () => (
  <svg
    width="40"
    height="40"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    className="logo-svg"
  >
    {/* Center Building */}
    <rect
      x="8"
      y="3"
      width="8"
      height="19"
      rx="2"
      stroke="#ccff00"
      strokeWidth="2.5"
      strokeLinecap="round"
      strokeLinejoin="round"
    />

    {/* Left Wing */}
    <path
      d="M4 22V12C4 10.8954 4.89543 10 6 10H8"
      stroke="#ccff00"
      strokeWidth="2.5"
      strokeLinecap="round"
      strokeLinejoin="round"
    />

    {/* Right Wing */}
    <path
      d="M20 22V12C20 10.8954 19.1046 10 18 10H16"
      stroke="#ccff00"
      strokeWidth="2.5"
      strokeLinecap="round"
      strokeLinejoin="round"
    />

    {/* Door */}
    <path
      d="M10 22V18C10 16.8954 10.8954 16 12 16C13.1046 16 14 16.8954 14 18V22"
      stroke="#ccff00"
      strokeWidth="2.5"
      strokeLinecap="round"
      strokeLinejoin="round"
    />

    {/* Windows */}
    <path
      d="M10 8H14"
      stroke="#ccff00"
      strokeWidth="2.5"
      strokeLinecap="round"
      strokeLinejoin="round"
    />
    <path
      d="M10 12H14"
      stroke="#ccff00"
      strokeWidth="2.5"
      strokeLinecap="round"
      strokeLinejoin="round"
    />
  </svg>
);
