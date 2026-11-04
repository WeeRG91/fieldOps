const utcFormatter = new Intl.DateTimeFormat('en-GB', {
    dateStyle: 'medium',
    timeStyle: 'long',
    timeZone: 'UTC',
});

export function formatUtc(value: string): string {
    return utcFormatter.format(new Date(value));
}
