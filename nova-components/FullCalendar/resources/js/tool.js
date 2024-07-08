import Tool from './components/FullCalendar'

Nova.booting((app, store) => {
  Nova.inertia('FullCalendar', Tool)
})
