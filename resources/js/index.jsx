import './bootstrap';
import { createRoot } from 'react-dom/client';

import Test from "./components/Test.jsx";
import Toto from "./components/Toto";

const domNode = document.getElementById('hello-react');
const root = createRoot(domNode);
root.render(<Test />);

const domNode2 = document.getElementById('toto-react');
const root2 = createRoot(domNode2);
root2.render(<Toto />);
