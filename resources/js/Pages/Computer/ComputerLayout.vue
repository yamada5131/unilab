<script setup lang="ts">
import Computer from '@/Pages/Computer/Computer.vue';
import { Room } from '@/types';
import { computed, reactive } from 'vue';

const props = defineProps<{
    room: Room;
}>();
const clickedStatus = reactive<{
    [id: string]: boolean;
}>({});
const handleClick = (id: string) => (clickedStatus[id] = !clickedStatus[id]);

// define seat arrays using refs
const leftseats = computed(() => props.room.computers?.slice(0, 24) || []);
const rightseats = computed(() => props.room.computers?.slice(24, 48) || []);
</script>

<template>
    <div class="flex min-h-svh" style="min-height: calc(100svh - 6rem)">
        <div class="grid basis-3/4 grid-cols-2 place-items-center p-4 sm:p-6">
            <!-- row 1 -->
            <div class="grid grid-cols-4 grid-rows-6 place-items-center gap-4">
                <Computer
                    v-for="(computer, index) in leftseats"
                    :index="index + 1"
                    :key="computer.id"
                    :computer="computer"
                    :isClicked="clickedStatus[computer.id] || false"
                    @click="handleClick(computer.id)"
                />
            </div>

            <!-- row 2 -->
            <div class="grid grid-cols-4 grid-rows-6 place-items-center gap-4">
                <Computer
                    v-for="(computer, index) in rightseats"
                    :key="computer.id"
                    :index="index + 25"
                    :computer="computer"
                    :isClicked="clickedStatus[computer.id] || false"
                    @click="handleClick(computer.id)"
                />
            </div>
        </div>
        <div class="basis-1/4 bg-gray-200">
            <h1 class="p-10 text-center text-2xl font-semibold">
                Phòng {{ room.name }}
            </h1>
        </div>
    </div>
</template>

<style scoped></style>
