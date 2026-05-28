export const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'short',
        day: 'numeric',
    });
};

export const toDate = (iso: string) => iso.slice(0, 10);
