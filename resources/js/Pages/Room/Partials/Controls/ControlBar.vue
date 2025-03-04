<template>
    <div class="flex flex-wrap gap-2">
        <Button
            variant="outline"
            size="sm"
            :disabled="!hasSelectedMachines"
            @click="executeCommand('lock')"
        >
            <LockIcon class="mr-1 h-4 w-4" />
            Lock
        </Button>
        <Button
            variant="outline"
            size="sm"
            :disabled="!hasSelectedMachines"
            @click="executeCommand('power_on')"
        >
            <PowerIcon class="mr-1 h-4 w-4" />
            Power On
        </Button>
        <Button
            variant="outline"
            size="sm"
            :disabled="!hasSelectedMachines"
            @click="executeCommand('reboot')"
        >
            <RefreshCwIcon class="mr-1 h-4 w-4" />
            Reboot
        </Button>
        <Button
            variant="outline"
            size="sm"
            :disabled="!hasSelectedMachines"
            @click="executeCommand('power_down')"
        >
            <PowerOffIcon class="mr-1 h-4 w-4" />
            Power Down
        </Button>
        <Button
            variant="outline"
            size="sm"
            :disabled="!hasSelectedMachines"
            @click="executeCommand('log_off')"
        >
            <LogOutIcon class="mr-1 h-4 w-4" />
            Log Off
        </Button>
        <Button
            variant="outline"
            size="sm"
            :disabled="!hasSelectedMachines"
            @click="executeCommand('screenshot')"
        >
            <ImageIcon class="mr-1 h-4 w-4" />
            Screenshot
        </Button>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useToast } from '@/components/ui/toast/use-toast';
import { router } from '@inertiajs/vue3';
import {
    ImageIcon,
    LockIcon,
    LogOutIcon,
    PowerIcon,
    RefreshCwIcon,
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    selectedMachines: string[];
}>();

const { toast } = useToast();
const hasSelectedMachines = computed(() => props.selectedMachines.length > 0);

const executeCommand = (commandType: string) => {
    if (props.selectedMachines.length === 0) return;

    props.selectedMachines.forEach((machineId) => {
        router.post(
            route('commands.store', {
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
};
</script>
