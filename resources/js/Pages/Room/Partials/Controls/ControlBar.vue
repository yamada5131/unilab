<!-- TODO: Đọc phần ControlBar này -->
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
import { useMachineStore } from '@/stores/machine';
import {
    ImageIcon,
    LockIcon,
    LogOutIcon,
    PowerIcon,
    PowerOffIcon,
    RefreshCwIcon,
} from 'lucide-vue-next';
import { computed } from 'vue';

// Use the machine store instead of props
const machineStore = useMachineStore();

const hasSelectedMachines = computed(
    () => machineStore.selectedMachines.length > 0,
);

const executeCommand = (commandType: string) => {
    machineStore.executeCommand(commandType);
};
</script>
