<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ComputerLayout from '@/Pages/Computer/ComputerLayout.vue';
import { Room } from '@/types';

const props = defineProps<{
    room: Room;
}>();
</script>

<template>
    <AuthenticatedLayout>
        <div
            class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <ComputerLayout :room="room" />
        </div>
        <!-- <Widget class="mt-4"> -->
        <!--     <div class="relative overflow-x-auto shadow-md sm:rounded-lg"> -->
        <!--         <div class="pb-4"> -->
        <!--             <label for="table-search" class="sr-only">Search</label> -->
        <!--             <div class="relative mt-1"> -->
        <!--                 <div -->
        <!--                     class="rtl:inset-r-0 pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3" -->
        <!--                 > -->
        <!--                     <svg -->
        <!--                         class="h-4 w-4 text-gray-500 dark:text-gray-400" -->
        <!--                         aria-hidden="true" -->
        <!--                         xmlns="http://www.w3.org/2000/svg" -->
        <!--                         fill="none" -->
        <!--                         viewBox="0 0 20 20" -->
        <!--                     > -->
        <!--                         <path -->
        <!--                             stroke="currentColor" -->
        <!--                             stroke-linecap="round" -->
        <!--                             stroke-linejoin="round" -->
        <!--                             stroke-width="2" -->
        <!--                             d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" -->
        <!--                         /> -->
        <!--                     </svg> -->
        <!--                 </div> -->
        <!--                 <input -->
        <!--                     id="table-search" -->
        <!--                     type="text" -->
        <!--                     class="block w-80 rounded-lg border border-gray-300 bg-gray-50 ps-10 pt-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500" -->
        <!--                     placeholder="Search for items" -->
        <!--                 /> -->
        <!--             </div> -->
        <!--         </div> -->
        <!--         <table -->
        <!--             class="w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400" -->
        <!--         > -->
        <!--             <thead -->
        <!--                 class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400" -->
        <!--             > -->
        <!--                 <tr> -->
        <!--                     <th scope="col" class="p-4"> -->
        <!--                         <div class="flex items-center"> -->
        <!--                             <input -->
        <!--                                 id="checkbox-all-search" -->
        <!--                                 type="checkbox" -->
        <!--                                 class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" -->
        <!--                             /> -->
        <!--                             <label -->
        <!--                                 for="checkbox-all-search" -->
        <!--                                 class="sr-only" -->
        <!--                             > -->
        <!--                                 checkbox -->
        <!--                             </label> -->
        <!--                         </div> -->
        <!--                     </th> -->
        <!--                     <th scope="col" class="px-6 py-3">Computer ID</th> -->
        <!--                     <th scope="col" class="px-6 py-3"> -->
        <!--                         Hardware Specifications -->
        <!--                     </th> -->
        <!--                     <th scope="col" class="px-6 py-3">MAC address</th> -->
        <!--                     <th scope="col" class="px-6 py-3"> -->
        <!--                         Operating System -->
        <!--                     </th> -->
        <!--                     <th scope="col" class="px-6 py-3"> -->
        <!--                         Installed Software -->
        <!--                     </th> -->
        <!--                     <th scope="col" class="px-6 py-3">Status</th> -->
        <!--                     <th scope="col" class="px-6 py-3">Action</th> -->
        <!--                 </tr> -->
        <!--             </thead> -->
        <!--             <tbody> -->
        <!--                 <tr -->
        <!--                     v-for="(computer, index) in props.computers.data" -->
        <!--                     :key="index" -->
        <!--                     class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600" -->
        <!--                 > -->
        <!--                     <td class="w-4 p-4"> -->
        <!--                         <div class="flex items-center"> -->
        <!--                             <input -->
        <!--                                 id="checkbox-table-search-1" -->
        <!--                                 type="checkbox" -->
        <!--                                 class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" -->
        <!--                             /> -->
        <!--                             <label -->
        <!--                                 for="checkbox-table-search-1" -->
        <!--                                 class="sr-only" -->
        <!--                             > -->
        <!--                                 checkbox -->
        <!--                             </label> -->
        <!--                         </div> -->
        <!--                     </td> -->
        <!--                     <th -->
        <!--                         scope="row" -->
        <!--                         class="whitespace-nowrap px-6 py-4 font-medium text-gray-900 dark:text-white" -->
        <!--                     > -->
        <!--                         {{ computer.id }} -->
        <!--                     </th> -->
        <!--                     <td class="px-6 py-4"> -->
        <!--                         <ul> -->
        <!--                             <li -->
        <!--                                 v-for="( -->
        <!--                                     value, key -->
        <!--                                 ) in parseSpecifications( -->
        <!--                                     computer.hardware_specifications, -->
        <!--                                 )" -->
        <!--                                 :key="key" -->
        <!--                             > -->
        <!--                                 <strong>{{ key }}:</strong> -->
        <!--                                 {{ value }} -->
        <!--                             </li> -->
        <!--                         </ul> -->
        <!--                     </td> -->
        <!--                     <td class="px-6 py-4"> -->
        <!--                         {{ computer.mac_address }} -->
        <!--                     </td> -->
        <!--                     <td class="px-6 py-4"> -->
        <!--                         {{ computer.operating_system }} -->
        <!--                     </td> -->
        <!--                     <td class="px-6 py-4"> -->
        <!--                         {{ computer.installed_software }} -->
        <!--                     </td> -->
        <!--                     <td class="px-6 py-4"> -->
        <!--                         {{ computer.status.status }} -->
        <!--                     </td> -->
        <!--                     <td class="px-6 py-4"> -->
        <!--                         <a -->
        <!--                             href="#" -->
        <!--                             class="font-medium text-blue-600 hover:underline dark:text-blue-500" -->
        <!--                         > -->
        <!--                             Edit -->
        <!--                         </a> -->
        <!--                         <a -->
        <!--                             href="#" -->
        <!--                             class="ms-3 font-medium text-red-600 hover:underline dark:text-red-500" -->
        <!--                         > -->
        <!--                             Delete -->
        <!--                         </a> -->
        <!--                     </td> -->
        <!--                 </tr> -->
        <!--             </tbody> -->
        <!--         </table> -->
        <!--     </div> -->
        <!---->
        <!--     <Pagination :elements="props.computers"></Pagination> -->
        <!-- </Widget> -->
    </AuthenticatedLayout>
</template>

<style scoped></style>
