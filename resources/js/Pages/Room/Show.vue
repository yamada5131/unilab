<template>
    <AuthenticatedLayout>
        <!-- Cung cấp nội dung cho header-grid slot -->
        <template #header-grid>
            <div class="flex flex-col gap-3">
                <h1 class="text-2xl font-bold">{{ room.data.name }} Layout</h1>
                <!-- Hiển thị thanh điều khiển ngang -->
                <ControlBar :selectedMachines="selectedMachines" />
            </div>
        </template>
        <MachineList
            :room="room.data"
            @update:selected="updateSelectedMachines"
        />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Room } from '@/types';
import { ref } from 'vue';
import ControlBar from './Partials/Controls/ControlBar.vue';
import MachineList from './Partials/Machines/MachineList.vue';

defineProps<{
    room: { data: Room };
}>();

const selectedMachines = ref<string[]>([]);

const updateSelectedMachines = (machines: string[]) => {
    selectedMachines.value = machines;
};
</script>
