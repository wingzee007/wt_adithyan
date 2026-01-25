const API_KEY="************** ****** ************";
const messages = document.getElementById("messages");
const input = document.getElementById("user-input");
const sendBtn = document.getElementById("send-btn");
const micBtn = document.getElementById("mic-btn");

/***********************
 💬 MESSAGE UI
************************/
function addMessage(text, type) {
  const msg = document.createElement("div");
  msg.className = `msg ${type}`;
  msg.textContent = text;
  messages.appendChild(msg);
  messages.scrollTop = messages.scrollHeight;
  return msg;
}

/***********************
 🎤 SPEECH TO TEXT
************************/
const SpeechRecognition =
  window.SpeechRecognition || window.webkitSpeechRecognition;

let recognition = null;
let isListening = false;

if (!SpeechRecognition) {
  alert("Speech Recognition NOT supported. Use Chrome or Edge.");
} else {
  recognition = new SpeechRecognition();
  recognition.lang = "en-US";
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;

  recognition.onstart = () => {
    isListening = true;
    micBtn.classList.add("listening");
    console.log("🎤 Listening...");
  };

  recognition.onresult = event => {
    const spokenText = event.results[0][0].transcript;
    console.log("🗣️ You said:", spokenText);

    input.value = spokenText;
    recognition.stop();       // 🔥 stop mic before fetch
    sendMessage();
  };

  recognition.onerror = event => {
    isListening = false;
    console.error("Speech error:", event.error);
    alert("Mic error: " + event.error);
  };

  recognition.onend = () => {
    isListening = false;
    micBtn.classList.remove("listening");
    console.log("🛑 Stopped listening");
  };
}

/***********************
 🎙 MIC BUTTON
************************/
micBtn.onclick = () => {
  if (!recognition || isListening) return;
  recognition.start();
};

/***********************
 🔊 TEXT TO SPEECH
************************/
function speak(text) {
  // Cancel any ongoing speech to avoid mic conflict
  window.speechSynthesis.cancel();

  const speech = new SpeechSynthesisUtterance(text);
  speech.lang = "en-US";
  speech.rate = 1;
  speech.pitch = 1;

  window.speechSynthesis.speak(speech);
}

/***********************
 📤 SEND MESSAGE
************************/
sendBtn.onclick = sendMessage;

input.addEventListener("keydown", e => {
  if (e.key === "Enter") sendMessage();
});

async function sendMessage() {
  const text = input.value.trim();
  if (!text) return;

  addMessage(text, "user");
  input.value = "";

  const typing = addMessage("Typing...", "bot typing");

  try {
    const response = await fetch(
      "https://api.openai.com/v1/chat/completions",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer " + API_KEY
        },
        body: JSON.stringify({
          model: "gpt-4o-mini",
          messages: [
            {
              role: "system",
              content:
                "You are a smart AI learning assistant. Explain clearly and keep answers structured."
            },
            { role: "user", content: text }
          ]
        })
      }
    );

    const data = await response.json();
    typing.remove();

    const reply =
      data.choices?.[0]?.message?.content ||
      "I couldn't understand that.";

    addMessage(reply, "bot");
    speak(reply); // 🔊 bot speaks

  } catch (err) {
    typing.remove();
    addMessage("Error connecting to AI.", "bot");
    console.error(err);
  }
}
