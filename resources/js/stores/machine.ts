//  TODO: Đọc lại phần Store này
import { useToast } from '@/components/ui/toast/use-toast';
import { router } from '@inertiajs/vue3';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMachineStore = defineStore('machine', () => {
    const { toast } = useToast();
    const selectedMachines = ref<string[]>([]);

    function selectMachines(machines: string[]) {
        selectedMachines.value = machines;
    }

    function toggleMachineSelection(machineId: string) {
        const index = selectedMachines.value.indexOf(machineId);
        if (index === -1) {
            selectedMachines.value.push(machineId);
        } else {
            selectedMachines.value.splice(index, 1);
        }
    }

    function clearSelection() {
        selectedMachines.value = [];
    }

    function executeCommand(commandType: string) {
        if (selectedMachines.value.length === 0) return;

        selectedMachines.value.forEach((machineId) => {
            router.post(
                route('commands.store.computer', {
                    machine_id: machineId,
                }),
                {
                    command_type: commandType.toUpperCase(),
                },
                {
                    onSuccess: () => {
                        toast({
                            title: `Command executed`,
                            description: `${commandType.toUpperCase()} command sent to machine ${machineId}`,
                        });
                    },
                    onError: (errors) => {
                        toast({
                            title: 'Error executing command',
                            description: Object.values(errors).join(', '),
                            variant: 'destructive',
                        });
                    },
                },
            );
        });
    }

    return {
        selectedMachines,
        selectMachines,
        toggleMachineSelection,
        clearSelection,
        executeCommand,
    };
});
