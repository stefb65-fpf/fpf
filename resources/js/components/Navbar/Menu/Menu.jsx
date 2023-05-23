import "./menu.css"
import {useState} from "react";
export default function Menu({selectedCardInfos}){
    const [selectedMenu,setSelectedMenu]=useState('accueil')
    const menu = [
        {
            id: 1,
            name: "Mon Profil",
            slug:"mon-profil",

        },
        {
            id: 2,
            name: "Formations",
            slug:"formations",

        },
        {
            id: 3,
            name: "Gestion Club",
            slug:"gestion-club",

        },
        {
            id: 4,
            name: "Gestion ur",
            slug:"gestion-ur",

        },
        {
            id: 5,
            name: "Gestion FPF",
            slug:"gestion-fpf",

        },
    ]
    return(
        <>
            {
                menu.map((item)=>

                        selectedCardInfos.menus.includes(item.id)&&
                            <li className={
                                "menuItem" + (setSelectedMenu ===item.name ? " active" : "")
                            }>
                                <a href={item.slug}>
                                    <div className="title">
                                        {item.name}
                                    </div>
                                </a>
                            </li>



                )
            }
        </>
    )
}
