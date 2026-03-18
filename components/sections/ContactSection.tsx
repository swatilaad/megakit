'use client';

import { useState } from 'react';
import type { ContactPage } from '@/types/wordpress';

interface ContactSectionProps {
  data?: ContactPage | null;
}

export default function ContactSection({ data }: ContactSectionProps) {
  if (!data) return null;

  const [formData, setFormData] = useState({ name: '', email: '', message: '' });
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: { preventDefault(): void }) => {
    e.preventDefault();
    setLoading(true);
    // In production: integrate with a form handler or WP REST API
    await new Promise((r) => setTimeout(r, 1000));
    setSubmitted(true);
    setLoading(false);
  };

  return (
    <section className="contact-form-wrap section">
      <div className="container">
        <div className="row">
          <div className="col-lg-6 col-md-12 col-sm-12">
            <form id="contact-form" className="contact__form" onSubmit={handleSubmit}>
              <div className="row">
                <div className="col-12">
                  {submitted && (
                    <div className="alert alert-success contact__msg" role="alert">
                      Your message was sent successfully.
                    </div>
                  )}
                </div>
              </div>
              <span className="text-color">Send a message</span>
              <h3 className="text-md mb-4">Contact Form</h3>
              <div className="form-group">
                <input
                  name="name"
                  type="text"
                  className="form-control"
                  placeholder="Your Name"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  required
                />
              </div>
              <div className="form-group">
                <input
                  name="email"
                  type="email"
                  className="form-control"
                  placeholder="Email Address"
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  required
                />
              </div>
              <div className="form-group-2 mb-4">
                <textarea
                  name="message"
                  className="form-control"
                  rows={4}
                  placeholder="Your Message"
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  required
                ></textarea>
              </div>
              <button className="btn btn-main" type="submit" disabled={loading}>
                {loading ? 'Sending...' : 'Send Message'}
              </button>
            </form>
          </div>

          <div className="col-lg-5 col-sm-12">
            <div className="contact-content pl-lg-5 mt-5 mt-lg-0">
              <span className="text-muted">We are Professionals</span>
              <h2 className="mb-5 mt-2">
                Don&apos;t Hesitate to contact with us for any kind of information
              </h2>
              <ul className="address-block list-unstyled">
                {data.address && (
                  <li>
                    <i className="ti-direction mr-3"></i>
                    {data.address}
                  </li>
                )}
                {data.email && (
                  <li>
                    <i className="ti-email mr-3"></i>Email: {data.email}
                  </li>
                )}
                {data.phone && (
                  <li>
                    <i className="ti-mobile mr-3"></i>Phone: {data.phone}
                  </li>
                )}
              </ul>
              <ul className="social-icons list-inline mt-5">
                {data.facebookUrl && (
                  <li className="list-inline-item">
                    <a href={data.facebookUrl}>
                      <i className="fab fa-facebook-f"></i>
                    </a>
                  </li>
                )}
                {data.twitterUrl && (
                  <li className="list-inline-item">
                    <a href={data.twitterUrl}>
                      <i className="fab fa-twitter"></i>
                    </a>
                  </li>
                )}
                {data.linkedinUrl && (
                  <li className="list-inline-item">
                    <a href={data.linkedinUrl}>
                      <i className="fab fa-linkedin-in"></i>
                    </a>
                  </li>
                )}
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
