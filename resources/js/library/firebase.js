//Set up key và config theo firebase cung cấp
import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyBCOzuBfeEwhs1Ybnn2Q9hFoPf2NnSDKuE",
  authDomain: "pharma24h-f0cd2.firebaseapp.com",
  projectId: "pharma24h-f0cd2",
  storageBucket: "pharma24h-f0cd2.firebasestorage.app",
  messagingSenderId: "989050282805",
  appId: "1:989050282805:web:cecb5ec012be833b67227c",
  measurementId: "G-21D5BKCZX2"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Authentication
const auth = getAuth(app);

export { auth };
export default app;
