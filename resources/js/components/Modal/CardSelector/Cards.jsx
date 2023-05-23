import "./cards.css"
export default function Cards({selectedCardInfos,setSelectedCardInfos, setShowModal}){
    const cards = [
        {
            id: 1,
            number: "02-0365-0036",
            abonnement : true,
            abonnementFeatures: "abonné FP jusqu'au numéro 294",
            menus:[1,2,3,4,5]
        },
        {
            id: 2,
            number: "10-0026-0003",
            abonnement : false,
            abonnementFeatures: "",
            menus:[1,2]
        }
    ]
    const changeCard = ( clickedCard )=>{
        if(selectedCardInfos.id !== clickedCard.id){
            setSelectedCardInfos(clickedCard)
            setShowModal(false)
        }
    }

    return(
        <>
            <div className="title">Vos cartes</div>
            <div className="cardSelector">

                {
                    cards.map((card) => (
                        <div className={
                            "card" + (selectedCardInfos.id == card.id ? " selected" : "")
                        } key={card.id} onClick={()=>{changeCard(card)}}>  <span>  N°</span> {card.number}</div>
                    ))
                }

            </div>
    </>

    )
}
