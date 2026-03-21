import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

const firebaseConfig = {
  apiKey: "AIzaSyAJozqvhIX8MbVTeeM69XL0ZD1cJWlTd3I",
  authDomain: "pharma24h-f0cd2.firebaseapp.com",
  projectId: "pharma24h-f0cd2",
  storageBucket: "pharma24h-f0cd2.firebasestorage.app",
  messagingSenderId: "989050282805",
  appId: "1:989050282805:web:cecb5ec012be833b67227c",
  measurementId: "G-21D5BKCZX2"
};

const app = initializeApp(firebaseConfig);

const auth = getAuth(app);

export { auth };
export default app;
