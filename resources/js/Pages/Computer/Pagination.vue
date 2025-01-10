<template>
    <div
        id="pagination"
        class="flex w-full items-center justify-center border-t border-gray-100 pt-4"
    >
        <div class="-mb-1 flex flex-wrap">
            <template v-for="(link, key) in elements.links" :key="key">
                <div
                    v-if="link.url === null"
                    class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 text-gray-400"
                    v-html="link.label"
                />
                <Link
                    v-else
                    class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 hover:bg-gray-200 focus:border-indigo-500 focus:text-indigo-500"
                    :class="{ 'bg-gray-500 text-white': link.active }"
                    :href="link.url"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
defineProps(["elements"]);
</script>
