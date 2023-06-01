import Features from "./components/Navbar/Features/Features";
import Modal from "./components/Modal/Modal";
import {useEffect, useState} from "react";
import {createRoot} from "react-dom/client";
import Menu from "./components/Navbar/Menu/Menu";

export default function App() {
    const [selectedCardInfos, setSelectedCardInfos] = useState({
        id: 1,
        number: " 02-0365-0036",
        abonnement: true,
        abonnementFeatures: "abonné FP jusqu'au numéro 294",
        menus: [1, 2, 3, 4, 5]
    })
    const [showModal, setShowModal] = useState(false)
    console.log(showModal)
    const domNode2 = document.getElementById('modal');
    const root2 = createRoot(domNode2);
    root2.render(<Modal showModal={showModal} setShowModal={setShowModal} selectedCardInfos={selectedCardInfos}
                        setSelectedCardInfos={setSelectedCardInfos}/>);

    const domNode3 = document.getElementById('navMemberFeatures');
    const root3 = createRoot(domNode3);
    root3.render(<Features setShowModal={setShowModal} selectedCardInfos={selectedCardInfos}/>)

    const domNode4 = document.getElementById('navMenu');
    const root4 = createRoot(domNode4);
    root4.render(<Menu selectedCardInfos={selectedCardInfos}/>)


}
