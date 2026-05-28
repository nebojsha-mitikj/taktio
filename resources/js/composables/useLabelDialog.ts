import type { Label } from '@/types/labels/Label';
import { ref } from 'vue';

export function useLabelDialog() {
    const dialogOpen = ref(false);
    const editingLabel = ref<Label | null>(null);

    const editLabel = (label: Label): void => {
        editingLabel.value = label;
        dialogOpen.value = true;
    };

    const createLabel = (): void => {
        editingLabel.value = null;
        dialogOpen.value = true;
    };

    return {
        dialogOpen,
        editingLabel,
        editLabel,
        createLabel,
    };
}
