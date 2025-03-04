<script setup lang="ts">
import { useToast } from '@/components/ui/toast/use-toast';
import { Room } from '@/types';
import MachineDialog from './MachineDialog.vue';
import MachineItem from './MachineItem.vue';

const { toast } = useToast();

import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { computed, reactive, ref, watch } from 'vue';
import * as z from 'zod';

const formSchema = toTypedSchema(
    z.object({
        command: z
            .string()
            .min(10, {
                message: 'Command must be at least 10 characters.',
            })
            .max(160, {
                message: 'Command must not be longer than 30 characters.',
            }),
    }),
);

const { handleSubmit } = useForm({
    validationSchema: formSchema,
});

import { router } from '@inertiajs/vue3';

const props = defineProps<{
    room: Room;
}>();

const emit = defineEmits<{
    (e: 'update:selected', machines: string[]): void;
}>();

const selectedComputers = reactive<string[]>([]);

// Watch for changes in selectedComputers and emit them
watch(
    selectedComputers,
    (newValue) => {
        emit('update:selected', [...newValue]);
    },
    { deep: true },
);

const handleClick = (id: string, event: MouseEvent) => {
    const isSelected = selectedComputers.includes(id);

    // Không nhấn Ctrl: Chỉ chọn một phần tử
    if (!event.ctrlKey) {
        selectedComputers.splice(0, selectedComputers.length, id);
        if (isSelected) {
            selectedComputers.splice(0, 1); // Xóa nếu đã chọn
        }
        return;
    }

    // Nhấn Ctrl: Thêm hoặc xóa phần tử
    if (isSelected) {
        selectedComputers.splice(selectedComputers.indexOf(id), 1);
    } else {
        selectedComputers.push(id);
    }
};

// Create grid representation
const gridCells = computed(() => {
    const cells = [];

    for (let row = 1; row <= props.room.grid_rows; row++) {
        const rowCells = [];
        for (let col = 1; col <= props.room.grid_cols; col++) {
            // Find if a computer exists at this position
            const computer = props.room.machines.find(
                (c) => c.pos_row === row && c.pos_col === col,
            );

            rowCells.push({
                row,
                col,
                computer,
                index: (row - 1) * props.room.grid_cols + col,
            });
        }
        cells.push(rowCells);
    }

    return cells;
});

// Add computer dialog state
const isComputerDialogOpen = ref(false);
const selectedPosition = ref({ row: 1, col: 1 });
const computerDialogFormId = 'add-computer-form';

const handleAddComputer = (row: number, col: number) => {
    selectedPosition.value = { row, col };
    isComputerDialogOpen.value = true;
};

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

const closeComputerDialog = () => {
    isComputerDialogOpen.value = false;
};
</script>

<template>
    <!-- min-h-[calc(100vh-5rem)]  -->
    <div class="relative flex h-full overflow-hidden p-4">
        <!-- Dynamic grid based on room dimensions -->
        <div class="flex h-full w-full items-center justify-center">
            <div
                class="grid grid-flow-row auto-rows-min gap-5"
                :style="{
                    // gridTemplateRows: `repeat(${room.grid_rows}, minmax(0, 1fr))`,
                    gridTemplateColumns: `repeat(${room.grid_cols}, min-content)`,
                }"
            >
                <template v-for="row in gridCells" :key="row[0].row">
                    <template v-for="cell in row" :key="cell.index">
                        <!-- If computer exists at this position -->
                        <MachineItem
                            v-if="cell.computer"
                            :key="`computer-${cell.row}-${cell.col}`"
                            :index="cell.index"
                            :machine="cell.computer"
                            :isSelected="
                                selectedComputers.includes(cell.computer.id)
                            "
                            @click="handleClick(cell.computer.id, $event)"
                        />

                        <!-- Empty slot with + sign -->
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

        <!-- Add Computer Dialog -->
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
