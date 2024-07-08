import Tool from './components/OpenAI'

Nova.booting((app, store) => {
  Nova.inertia('Openaichat', Tool)
})
