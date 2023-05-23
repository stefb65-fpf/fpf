import "./modal.css"
import Cards from "./CardSelector/Cards";
export default function Modal({showModal,setShowModal, selectedCardInfos,setSelectedCardInfos}){

    return(
        <div className={
            "background" + (showModal ? " visible" : "")
        }>

            <div className="container">
                <div className="close" onClick={()=>{setShowModal(false)}}>
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="19.5" stroke="#1B3B78" stroke-opacity="0.78"/>
                        <path d="M9.00122 9L30.5535 30.5523" stroke="#1B3B78" stroke-opacity="0.78" stroke-linecap="round"/>
                        <path d="M30.5522 9L8.99998 30.5523" stroke="#1B3B78" stroke-opacity="0.78" stroke-linecap="round"/>
                    </svg>
                </div>
                <div className="wrapper">
                   <Cards selectedCardInfos={selectedCardInfos} setSelectedCardInfos={setSelectedCardInfos} setShowModal={setShowModal}/>

                </div>

            </div>
        </div>


    )
}
