<script setup lang="ts">
import { Machine } from '@/types';
const props = defineProps<{
    index: number;
    machine: Machine;
    isSelected: boolean;
}>();
</script>

<template>
    <div
        :class="[
            'flex h-14 w-14 cursor-pointer select-none items-center justify-center rounded bg-gray-200 text-center font-semibold transition-transform hover:scale-105',
            props.machine.is_online && 'border-4 border-green-500',
            !props.machine.is_online && 'border-4 border-red-500',
            props.isSelected && 'scale-110 !bg-blue-500 text-white shadow-lg', // Hiệu ứng khi được chọn
        ]"
    >
        {{ props.index }}
    </div>
</template>

<style scoped></style>
