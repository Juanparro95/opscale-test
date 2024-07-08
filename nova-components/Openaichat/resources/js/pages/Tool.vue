<template>
  <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">OpenAI Chat</h1>

      <div class="mb-4">
          <h2 class="text-xl font-semibold">Chat History</h2>
          <ul>
              <li v-for="(message, index) in messages" :key="index">
                  <strong>{{ message.role }}:</strong> {{ message.content }}
              </li>
          </ul>
      </div>
      <br>
      <div>
          <textarea v-model="userInput" placeholder="Type your message here..." class="w-full p-2 border rounded"></textarea>
          <button @click="sendMessage" class="mt-2 p-2 bg-green-500 text-white rounded">Send</button>
      </div>     
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
      return {
          userInput: '',
          messages: [],
      };
  },
  methods: {
      async sendMessage() {
          if (this.userInput.trim() === '') {
              return;
          }

          this.messages.push({ role: 'User:', content: this.userInput });

          try {
              const response = await axios.post('/nova-vendor/openaichat/send', {
                  message: this.userInput,
              });

              this.messages.push({ role: 'ChatGPT:', content: response.data });
          } catch (error) {
              console.error('Error sending message:', error);
          }

          this.userInput = '';
      },
  },
};
</script>

<style>
/* Add some basic styling */
.container {
  max-width: 600px;
  margin: auto;
}
</style>
