<script setup lang="ts">
import { useToast } from '@/components/ui/toast/use-toast';
import { useMachineStore } from '@/stores/machine';
import { Room } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import MachineDialog from './MachineDialog.vue';
import MachineItem from './MachineItem.vue';

// Initialize toast notification system
const { toast } = useToast();

// Component props - accepts room data that contains the grid dimensions and machines
const props = defineProps<{
    room: Room;
}>();

// Use centralized machine store for state management instead of local component state
const machineStore = useMachineStore();

/**
 * Handles machine selection with the following behaviors:
 * - Normal click: Select only the clicked machine
 * - Ctrl+click: Toggle selection of the clicked machine (multi-select)
 * @param id The ID of the machine being clicked
 * @param event Mouse event to check for modifier keys
 */
const handleClick = (id: string, event: MouseEvent) => {
    const isSelected = machineStore.selectedMachines.includes(id);

    // Without Ctrl key: Select only this machine (or deselect if already selected)
    if (!event.ctrlKey) {
        machineStore.selectMachines(isSelected ? [] : [id]);
        return;
    }

    // With Ctrl key: Toggle this machine's selection (add/remove from multi-selection)
    machineStore.toggleMachineSelection(id);
};

/**
 * Creates a 2D grid representation of the room
 * Each cell contains information about:
 * - Its position (row, col)
 * - Machine at this position (if any)
 * - A linear index for rendering purposes
 */
const gridCells = computed(() => {
    const cells = [];

    for (let row = 1; row <= props.room.grid_rows; row++) {
        const rowCells = [];
        for (let col = 1; col <= props.room.grid_cols; col++) {
            // Find if a machine exists at this position
            const machine = props.room.machines.find(
                (m) => m.pos_row === row && m.pos_col === col,
            );

            rowCells.push({
                row,
                col,
                machine,
                index: (row - 1) * props.room.grid_cols + col, // Calculate linear index for key prop
            });
        }
        cells.push(rowCells);
    }

    return cells;
});

// State for the computer creation dialog
const isComputerDialogOpen = ref(false);
const selectedPosition = ref({ row: 1, col: 1 }); // Default position
const computerDialogFormId = 'add-computer-form';

/**
 * Opens the add computer dialog with the selected grid position
 * @param row Row position in the grid
 * @param col Column position in the grid
 */
const handleAddComputer = (row: number, col: number) => {
    selectedPosition.value = { row, col };
    isComputerDialogOpen.value = true;
};

/**
 * Handles the submission of the add computer form
 * Makes API request and shows success/error notifications
 * @param data Form data containing computer details
 */
const handleComputerSubmit = (data: any) => {
    router.post(route('computers.store'), data, {
        onSuccess: () => {
            isComputerDialogOpen.value = false;
            toast({
                title: 'Máy tính đã được thêm',
                description: `Máy ${data.name} đã được thêm thành công vào vị trí (${data.pos_row}, ${data.pos_col})`,
            });
        },
        onError: (errors) => {
            toast({
                title: 'Lỗi khi thêm máy tính',
                description: Object.values(errors).join(', '),
                variant: 'destructive',
            });
        },
    });
};

/**
 * Closes the computer creation dialog
 */
const closeComputerDialog = () => {
    isComputerDialogOpen.value = false;
};
</script>

<template>
    <!-- Room container with overflow handling to ensure grid fits properly -->
    <div class="relative flex h-full overflow-hidden p-4">
        <!-- Centered grid container -->
        <div class="flex h-full w-full items-center justify-center">
            <div
                class="grid grid-flow-row auto-rows-min gap-5"
                :style="{
                    // Dynamic column layout based on room configuration
                    gridTemplateColumns: `repeat(${room.grid_cols}, min-content)`,
                }"
            >
                <!-- Iterate through each row in our computed grid -->
                <template v-for="row in gridCells" :key="row[0].row">
                    <!-- Iterate through each cell in the current row -->
                    <template v-for="cell in row" :key="cell.index">
                        <!-- Display machine component if a machine exists at this position -->
                        <MachineItem
                            v-if="cell.machine"
                            :key="`machine-${cell.row}-${cell.col}`"
                            :index="cell.index"
                            :machine="cell.machine"
                            :isSelected="
                                machineStore.selectedMachines.includes(
                                    cell.machine.id,
                                )
                            "
                            @click="handleClick(cell.machine.id, $event)"
                        />

                        <!-- Display empty cell with "+" button if no machine exists -->
                        <div
                            v-else
                            :key="`empty-${cell.row}-${cell.col}`"
                            class="flex h-14 w-14 cursor-pointer select-none items-center justify-center rounded border-2 border-dashed border-gray-300 text-2xl text-gray-400 hover:border-gray-500 hover:text-gray-600"
                            @click="handleAddComputer(cell.row, cell.col)"
                        >
                            +
                        </div>
                    </template>
                </template>
            </div>
        </div>

        <!-- Modal dialog for adding a new computer - appears when clicking "+" -->
        <MachineDialog
            :form-id="computerDialogFormId"
            :is-open="isComputerDialogOpen"
            :position="selectedPosition"
            :room-id="room.id"
            @update:is-open="isComputerDialogOpen = $event"
            @submit="handleComputerSubmit"
            @close="closeComputerDialog"
        />
    </div>
</template>

<style scoped></style>
