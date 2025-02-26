<template>
    <AuthenticatedLayout>
        <!-- Cung cấp nội dung cho header-grid slot -->
        <template #header-grid>
            <h1 class="text-2xl font-bold">{{ room.name }} Layout</h1>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
            </div>
        </template>
        <div class="p-4">
            <!-- Grid Layout -->
            <div class="grid gap-2 rounded-lg bg-gray-100" :style="gridStyle">
                <div
                    v-for="(slot, index) in gridSlots"
                    :key="index"
                    class="aspect-square rounded-lg bg-white shadow-sm transition-shadow hover:shadow-md"
                >
                    <template v-if="slot.computer">
                        <div class="flex h-4 flex-col p-2">
                            <div class="flex justify-between">
                                <span class="font-medium">{{
                                    slot.computer.name
                                }}</span>
                                <div class="relative">
                                    <button class="rounded hover:bg-gray-100">
                                        ⋮
                                    </button>
                                    <div
                                        class="absolute right-0 w-48 rounded border bg-white py-2 shadow-lg"
                                    >
                                        <button
                                            class="block w-full px-4 py-2 text-left hover:bg-gray-50"
                                            @click="
                                                handleComputerAction(
                                                    slot.computer,
                                                    'powerOn',
                                                )
                                            "
                                        >
                                            Power On
                                        </button>
                                        <button
                                            class="block w-full px-4 py-2 text-left hover:bg-gray-50"
                                            @click="
                                                handleComputerAction(
                                                    slot.computer,
                                                    'remoteAccess',
                                                )
                                            "
                                        >
                                            Remote Access
                                        </button>
                                        <button
                                            class="block w-full px-4 py-2 text-left text-red-500 hover:bg-gray-50"
                                            @click="
                                                handleComputerAction(
                                                    slot.computer,
                                                    'remove',
                                                )
                                            "
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">{{
                                slot.computer.specs
                            }}</span>
                            <div class="mt-auto text-xs text-gray-400">
                                {{ slot.row }}-{{ slot.col }}
                            </div>
                        </div>
                    </template>
                    <button
                        v-else
                        class="flex h-full w-full items-center justify-center text-2xl text-gray-400 hover:bg-gray-50"
                        @click="openAddComputerModal(slot.row, slot.col)"
                    >
                        +
                    </button>
                </div>
            </div>

            <!-- Add Computer Modal -->
            <div
                v-if="showModal"
                class="fixed inset-0 flex items-center justify-center bg-black/50"
            >
                <div class="w-96 rounded-lg bg-white p-6">
                    <h3 class="mb-4 text-lg font-bold">
                        Add Computer (Position {{ selectedPosition.row }}-{{
                            selectedPosition.col
                        }})
                    </h3>
                    <form @submit.prevent="handleAddComputer">
                        <div class="space-y-4">
                            <div>
                                <label>Computer Name</label>
                                <input
                                    v-model="newComputer.name"
                                    type="text"
                                    class="w-full rounded border p-2"
                                    required
                                />
                            </div>
                            <div>
                                <label>Specifications</label>
                                <textarea
                                    v-model="newComputer.specs"
                                    class="w-full rounded border p-2"
                                    rows="3"
                                    required
                                />
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="rounded border px-4 py-2"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="rounded bg-blue-500 px-4 py-2 text-white"
                                >
                                    Add Computer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref } from 'vue';

// Dummy data
const room = ref({
    id: 'room-1',
    name: 'Lab 1',
    grid_rows: 5,
    grid_cols: 5,
});

const computers = ref([
    { id: 'pc-1', name: 'PC-01', specs: 'i7, 16GB', grid_row: 1, grid_col: 1 },
    { id: 'pc-2', name: 'PC-02', specs: 'i5, 8GB', grid_row: 2, grid_col: 3 },
]);

// Modal state
const showModal = ref(false);
const selectedPosition = ref({ row: null, col: null });
const newComputer = ref({
    name: '',
    specs: '',
});

// Computed
const gridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${room.value.grid_cols}, minmax(0, 1fr))`,
    gridTemplateRows: `repeat(${room.value.grid_rows}, minmax(0, 1fr))`,
}));

const gridSlots = computed(() => {
    const slots = [];
    for (let row = 1; row <= room.value.grid_rows; row++) {
        for (let col = 1; col <= room.value.grid_cols; col++) {
            const computer = computers.value.find(
                (c) => c.grid_row === row && c.grid_col === col,
            );
            slots.push({ row, col, computer });
        }
    }
    return slots;
});

// Methods
const openAddComputerModal = (row, col) => {
    selectedPosition.value = { row, col };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    newComputer.value = { name: '', specs: '' };
};

const handleAddComputer = () => {
    computers.value.push({
        id: `pc-${computers.value.length + 1}`,
        name: newComputer.value.name,
        specs: newComputer.value.specs,
        grid_row: selectedPosition.value.row,
        grid_col: selectedPosition.value.col,
    });
    closeModal();
};

const handleComputerAction = (computer, action) => {
    switch (action) {
        case 'powerOn':
            console.log('Powering on:', computer.name);
            break;
        case 'remoteAccess':
            console.log('Remote access to:', computer.name);
            break;
        case 'remove':
            computers.value = computers.value.filter(
                (c) => c.id !== computer.id,
            );
            break;
    }
};
</script>
