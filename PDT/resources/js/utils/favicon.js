/**
 * Dynamically update the browser favicon to follow the school's logo.
 * @param {string} url - URL or base64 data URI of the logo
 */
export function updateFavicon(url) {
  if (!url) return;
  try {
    let link = document.querySelector("link[rel*='icon']");
    if (!link) {
      link = document.createElement('link');
      link.rel = 'icon';
      link.type = 'image/png';
      document.head.appendChild(link);
    }
    link.href = url;
  } catch (e) {
    console.debug('Failed to update favicon:', e);
  }
}

