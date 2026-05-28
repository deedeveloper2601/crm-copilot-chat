<script setup>
import { ref, nextTick } from 'vue';
import axios from 'axios';

// Receive the lead email passed from the Laravel controller
const props = defineProps({
    leadEmail: {
        type: String,
        required: true
    }
});

const userInput = ref('');
const isTyping = ref(false);
const chatContainer = ref(null);

// Initialize chat history with a welcome message
const chatHistory = ref([
    { role: 'ai', text: `Hello! I am your VectorLead AI Copilot. I've analyzed our vector database records for ${props.leadEmail}. How can I assist you with this account today?` }
]);

// Quick suggestions to help the user test interactions
const suggestions = [
    { label: "Summarize recent notes", prompt: "Summarize our interactions with this lead so far." },
    { label: "Log follow-up call", prompt: "Log a call: Had a great sync today. Digambar is highly interested and wants a follow-up call next Tuesday at 2 PM." },
    { label: "Log new note", prompt: "Log a note: Digambar wants to review the Pro plan brochure before deciding." }
];

// Helper to keep the chat scrolled to the bottom
const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTo({
            top: chatContainer.value.scrollHeight,
            behavior: 'smooth'
        });
    }
};

const selectSuggestion = (promptText) => {
    userInput.value = promptText;
};

const sendMessage = async () => {
    const textToSend = userInput.value.trim();
    if (!textToSend) return;

    // 1. Add user message to UI
    chatHistory.value.push({ role: 'user', text: textToSend });
    userInput.value = '';
    isTyping.value = true;
    scrollToBottom();

    try {
        // 2. Send to Laravel Controller
        const response = await axios.post('/crm/copilot/ask', {
            prompt: textToSend,
            lead_email: props.leadEmail
        });

        // 3. Add AI response to UI
        chatHistory.value.push({ role: 'ai', text: response.data.answer });
    } catch (error) {
        console.error("AI Error:", error);
        chatHistory.value.push({ role: 'ai', text: 'Oops! The AI microservice is currently offline or encountered a system error. Please ensure main.py is running on port 8001.' });
    } finally {
        isTyping.value = false;
        scrollToBottom();
    }
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0f19] text-gray-100 font-sans antialiased py-10 px-4 relative overflow-hidden">
        
        <!-- Glowing Ambient Background Accents -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-500/10 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-violet-600/10 rounded-full blur-[150px] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
            
            <!-- Left Side / Main Chat Interface (Span 2) -->
            <div class="lg:col-span-2 bg-[#121826]/80 backdrop-blur-xl border border-gray-800/80 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
                
                <!-- Chat Header -->
                <div class="p-6 border-b border-gray-800 flex items-center justify-between bg-gradient-to-r from-gray-900/60 to-gray-800/20">
                    <div class="flex items-center gap-4">
                        <!-- AI Avatar with pulsing status indicator -->
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-[#121826] rounded-full animate-pulse"></span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-300 via-violet-200 to-white">VectorLead AI Copilot</h2>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                <span class="text-xs text-gray-400">Powered by Gemini & PGVector RAG</span>
                            </div>
                        </div>
                    </div>

                    <!-- Connection indicator badge -->
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-emerald-400">Active Session</span>
                    </div>
                </div>

                <!-- Chat Messages Window -->
                <div 
                    ref="chatContainer"
                    class="h-[480px] overflow-y-auto p-6 space-y-6 scrollbar-thin scrollbar-thumb-gray-800 scrollbar-track-transparent"
                >
                    <div 
                        v-for="(msg, index) in chatHistory" 
                        :key="index" 
                        class="flex w-full animate-fade-in"
                        :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
                    >
                        <div 
                            :class="msg.role === 'user' 
                                ? 'bg-gradient-to-br from-indigo-600 to-violet-600 text-white rounded-br-none shadow-md shadow-indigo-600/10' 
                                : 'bg-[#1b2336] text-gray-200 border border-gray-800/80 rounded-bl-none shadow-sm'" 
                            class="px-5 py-3.5 rounded-2xl max-w-[85%] whitespace-pre-wrap leading-relaxed text-[14px]"
                        >
                            {{ msg.text }}
                        </div>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div v-if="isTyping" class="flex justify-start">
                        <div class="bg-[#1b2336] border border-gray-800 text-gray-400 px-5 py-3.5 rounded-2xl rounded-bl-none shadow-sm text-[13px] flex items-center gap-3">
                            <div class="flex space-x-1">
                                <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                <div class="w-2.5 h-2.5 bg-violet-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                <div class="w-2.5 h-2.5 bg-fuchsia-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                            </div>
                            <span class="text-xs tracking-wide">AI is searching PostgreSQL & processing...</span>
                        </div>
                    </div>
                </div>

                <!-- Input & Actions Section -->
                <div class="p-6 border-t border-gray-800/60 bg-gray-900/20">
                    <!-- Quick Suggestions -->
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 font-semibold mb-2 uppercase tracking-wider">Suggested Actions</p>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                v-for="(suggest, idx) in suggestions"
                                :key="idx"
                                @click="selectSuggestion(suggest.prompt)"
                                class="text-xs px-3 py-2 bg-[#1b2336]/60 hover:bg-[#222c44] border border-gray-800/60 rounded-lg text-indigo-300 hover:text-white transition-all duration-200 text-left"
                            >
                                {{ suggest.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Input field -->
                    <div class="relative flex items-center">
                        <input 
                            v-model="userInput" 
                            @keyup.enter="sendMessage"
                            type="text" 
                            placeholder="Ask a question or log a new call/note..." 
                            class="w-full px-5 py-3.5 pr-28 bg-[#161d2d] border border-gray-800/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-[#1b2336] transition-all text-gray-200 placeholder-gray-500 text-sm"
                            :disabled="isTyping"
                        >
                        <button 
                            @click="sendMessage" 
                            :disabled="isTyping || !userInput.trim()"
                            class="absolute right-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold text-xs px-5 py-2.5 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 shadow-md shadow-indigo-600/10 flex items-center gap-1.5"
                        >
                            <span>Send</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side / Intelligence Panel (Span 1) -->
            <div class="space-y-6">
                <!-- Lead Insights Card -->
                <div class="bg-[#121826]/80 backdrop-blur-xl border border-gray-800/80 rounded-2xl p-6 shadow-2xl">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Lead CRM Profile
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-[#1b2336]/40 border border-gray-800/60 rounded-xl">
                            <span class="text-xs text-gray-500 block mb-0.5">Email Address</span>
                            <span class="text-sm font-semibold text-indigo-300 block truncate">{{ leadEmail }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-[#1b2336]/40 border border-gray-800/60 rounded-xl">
                                <span class="text-xs text-gray-500 block mb-0.5">Status</span>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-400 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    Warm Lead
                                </span>
                            </div>
                            <div class="p-3 bg-[#1b2336]/40 border border-gray-800/60 rounded-xl">
                                <span class="text-xs text-gray-500 block mb-0.5">Priority</span>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-400 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></span>
                                    High
                                </span>
                            </div>
                        </div>

                        <div class="p-4 bg-[#1b2336]/40 border border-gray-800/60 rounded-xl">
                            <span class="text-xs text-gray-500 block mb-2 font-semibold uppercase tracking-wider">Vector Index Info</span>
                            <div class="space-y-1.5 text-xs text-gray-400">
                                <div class="flex justify-between">
                                    <span>Distance Metric:</span>
                                    <span class="font-mono text-gray-300">Cosine Distance</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Vector Dimension:</span>
                                    <span class="font-mono text-indigo-400">384 Dimensions</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Database:</span>
                                    <span class="font-mono text-emerald-400">Postgres + PGVector</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Database Live Status Panel -->
                <div class="bg-[#121826]/80 backdrop-blur-xl border border-gray-800/80 rounded-2xl p-6 shadow-2xl">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        Vector Store Stats
                    </h3>
                    <div class="p-4 bg-[#1b2336]/30 border border-gray-800/50 rounded-xl space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Service URL:</span>
                            <span class="font-mono text-gray-300">127.0.0.1:8001</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">FastAPI Status:</span>
                            <span class="text-emerald-400 font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Online
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Table Name:</span>
                            <span class="font-mono text-indigo-400">crm_notes</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
/* Custom slim scrollbar */
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #1b2336;
    border-radius: 9999px;
}
::-webkit-scrollbar-thumb:hover {
    background: #2b3652;
}
</style>