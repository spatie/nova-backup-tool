import { renderToString } from '@vue/server-renderer';
import { createSSRApp, h } from 'vue';
import { expect, it } from 'vitest';
import Backup from '../../resources/js/components/Backup.vue';

it('renders the download action with a real Nova icon', async () => {
    const app = createSSRApp({
        render: () => h(Backup, {
            date: '2026-10-19 10:00:00',
            path: 'Laravel & Co/2026-10-19 #1.zip',
            size: '12 MB',
            disk: 'local & remote',
            deletable: false,
            deleting: false,
        }),
    });

    app.config.globalProperties.__ = value => value;

    const html = await renderToString(app);
    const downloadLink = html.match(/<a\b[^>]*>(.*?)<\/a>/s)?.[0];

    expect(downloadLink).toContain('title="Download"');
    const href = downloadLink.match(/href="([^"]+)"/)[1].replaceAll('&amp;', '&');
    const url = new URL(href, 'https://example.com');

    expect(url.searchParams.get('disk')).toBe('local & remote');
    expect(url.searchParams.get('path')).toBe('Laravel & Co/2026-10-19 #1.zip');
    expect(downloadLink).toMatch(/<svg\b[^>]*>[\s\S]*<path\b/);
});
