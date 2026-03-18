import type { NextConfig } from 'next';

const nextConfig: NextConfig = {
  images: {
    remotePatterns: [
      {
        // Allow images from any WordPress instance
        protocol: 'https',
        hostname: '**',
      },
      {
        protocol: 'http',
        hostname: '**',
      },
    ],
  },
  // Transpile packages that need it
  transpilePackages: ['react-slick'],
};

export default nextConfig;
