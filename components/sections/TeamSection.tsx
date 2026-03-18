import Image from 'next/image';
import type { TeamMember } from '@/types/wordpress';

interface TeamSectionProps {
  subtitle?: string;
  heading?: string;
  members?: TeamMember[];
}

export default function TeamSection({ subtitle, heading, members }: TeamSectionProps) {
  if (!members?.length) return null;

  return (
    <section className="section team">
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-lg-7 text-center">
            <div className="section-title">
              <span className="h6 text-color">{subtitle}</span>
              <h2 className="mt-3 content-title">{heading}</h2>
            </div>
          </div>
        </div>

        <div className="row justify-content-center">
          {members.map((member, i) => {
            const isLast3 = i >= members.length - 3;
            return (
              <div key={member.id} className="col-lg-4 col-md-6 col-sm-6">
                <div className={`team-item-wrap${isLast3 ? ' mb-5 mb-lg-0' : ' mb-5'}`}>
                  <div className="team-item position-relative">
                    {member.photo && (
                      <Image
                        src={member.photo.sourceUrl}
                        alt={member.photo.altText || member.name}
                        width={400}
                        height={400}
                        className="img-fluid w-100"
                        style={{ objectFit: 'cover' }}
                      />
                    )}
                    <div className="team-img-hover">
                      <ul className="team-social list-inline">
                        {member.facebookUrl && (
                          <li className="list-inline-item">
                            <a href={member.facebookUrl} className="facebook">
                              <i className="fab fa-facebook-f" aria-hidden="true"></i>
                            </a>
                          </li>
                        )}
                        {member.twitterUrl && (
                          <li className="list-inline-item">
                            <a href={member.twitterUrl} className="twitter">
                              <i className="fab fa-twitter" aria-hidden="true"></i>
                            </a>
                          </li>
                        )}
                        {member.instagramUrl && (
                          <li className="list-inline-item">
                            <a href={member.instagramUrl} className="instagram">
                              <i className="fab fa-instagram" aria-hidden="true"></i>
                            </a>
                          </li>
                        )}
                        {member.linkedinUrl && (
                          <li className="list-inline-item">
                            <a href={member.linkedinUrl} className="linkedin">
                              <i className="fab fa-linkedin-in" aria-hidden="true"></i>
                            </a>
                          </li>
                        )}
                      </ul>
                    </div>
                  </div>
                  <div className="team-item-content">
                    <h4 className="mt-3 mb-0 text-capitalize">{member.name}</h4>
                    <p>{member.role}</p>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
