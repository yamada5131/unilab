<template>
    <AuthenticatedLayout>
        <!-- Cung cấp nội dung cho header-grid slot -->
        <!-- <template #header-grid>
            <h1 class="text-2xl font-bold">{{ room.data.name }} Layout</h1>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
            </div>
        </template> -->
        <MachineList :room="room.data" />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Room } from '@/types';
import MachineList from './Partials/Machines/MachineList.vue';

defineProps<{
    room: { data: Room };
}>();
</script>
