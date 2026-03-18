interface WordPressErrorProps {
  error: string;
  pageName?: string;
}

export default function WordPressError({ error, pageName = 'this page' }: WordPressErrorProps) {
  const isConnectionError =
    error.toLowerCase().includes('fetch') ||
    error.toLowerCase().includes('network') ||
    error.toLowerCase().includes('econnrefused') ||
    error.toLowerCase().includes('failed');

  return (
    <div className="section">
      <div className="container">
        <div
          className="text-center p-5 rounded"
          style={{ background: '#f5f8f9', border: '1px solid #eee' }}
        >
          <i
            className="ti-alert text-color"
            style={{ fontSize: 48, display: 'block', marginBottom: 16 }}
          ></i>
          <h3 className="mb-3">
            {isConnectionError ? 'WordPress Connection Error' : 'Content Unavailable'}
          </h3>
          <p className="text-muted mb-4">
            {isConnectionError
              ? `Unable to connect to WordPress. Please ensure your WordPress instance is running and the NEXT_PUBLIC_WORDPRESS_API_URL environment variable is correctly configured.`
              : `Failed to load content for ${pageName}: ${error}`}
          </p>
          {isConnectionError && (
            <div
              className="text-left d-inline-block p-4 rounded"
              style={{ background: '#fff', border: '1px solid #ddd', maxWidth: 480 }}
            >
              <p className="mb-2">
                <strong>To fix this:</strong>
              </p>
              <ol className="mb-0" style={{ paddingLeft: 20 }}>
                <li>Ensure WordPress is running locally or on a server.</li>
                <li>
                  Set <code>NEXT_PUBLIC_WORDPRESS_API_URL</code> in your <code>.env.local</code>{' '}
                  file to your WPGraphQL endpoint (e.g.{' '}
                  <code>http://localhost/wp/graphql</code>).
                </li>
                <li>Install and activate WPGraphQL and WPGraphQL for ACF plugins.</li>
              </ol>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
