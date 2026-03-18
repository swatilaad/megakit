import type { Metadata } from 'next';
import './globals.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';
import { getGlobalSettings } from '@/lib/wordpress';

export const metadata: Metadata = {
  title: 'Megakit | Business Agency',
  description: 'Megakit - A modern business agency Next.js + Headless WordPress template.',
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const settingsResult = await getGlobalSettings();
  const settings = settingsResult.data;

  return (
    <html lang="en">
      <head>
        <link rel="stylesheet" href="/plugins/themify/css/themify-icons.css" />
        <link rel="stylesheet" href="/css/style.css" />
      </head>
      <body>
        <Header settings={settings} />
        {children}
        <Footer settings={settings} />
      </body>
    </html>
  );
}
