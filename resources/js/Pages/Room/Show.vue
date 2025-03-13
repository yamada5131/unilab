<template>
    <AuthenticatedLayout>
        <!-- Cung cấp nội dung cho header-grid slot -->
        <template #header-grid>
            <div class="flex flex-col gap-3">
                <h1 class="text-2xl font-bold">{{ room.data.name }} Layout</h1>
                <!-- Hiển thị thanh điều khiển ngang -->
                <ControlBar />
            </div>
        </template>
        <MachineList :room.data="room" />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { useMachineStore } from '@/stores/machine';
import { Room } from '@/types';
import { onMounted } from 'vue';
import ControlBar from './Partials/Controls/ControlBar.vue';
import MachineList from './Partials/Machines/MachineList.vue';

defineProps<{
    room: { data: Room };
}>();

const machineStore = useMachineStore();

onMounted(() => {
    // Clear any previous machine selections when mounting this component
    machineStore.clearSelection();
});
</script>
