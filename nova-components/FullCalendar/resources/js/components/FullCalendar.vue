<template>
  <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Calendario de Usuarios</h1>
      <FullCalendar :options="calendarOptions" />
  </div>
</template>

<script>
import { ref } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import axios from 'axios';

export default {
  components: {
      FullCalendar,
  },
  setup() {
      const calendarOptions = ref({
          plugins: [dayGridPlugin],
          initialView: 'dayGridMonth',
          events: [],
      });

      axios.get('/nova-vendor/full-calendar/users')
            .then(response => {
                calendarOptions.value.events = response.data.map(user => ({
                    title: user.name,
                    start: user.created_at,
                }));
            });

      return {
          calendarOptions,
      };
  },
};
</script>
