import React from 'react'

function Menu() {
  return (

<div className='flex items-center justify-center bg-gradient-to-br'>
  <div
            className="overflow-hidden bg-red-400 cursor-pointer rounded-xl relative group"
        >
            <div
                className="place-items-start  rounded-xl z-50 opacity-0 group-hover:opacity-100 transition duration-300 ease-in-out cursor-pointer absolute from-black/80 to-transparent bg-gradient-to-t inset-x-0 -bottom-2 pt-30 text-white flex items-end"
            >
                <div>
                    <div
                        className="transform-gpu  p-4 space-y-3 text-xl group-hover:opacity-100 group-hover:translate-y-0 translate-y-4 pb-10 transform transition duration-300 ease-in-out"
                    >
                        <div className="font-bold ">
                          <h1>Marcel Vrea Haine</h1>
                        </div>
                          
                        
                        
                    </div>
                </div>
            </div>
            <img
                alt=""
                className="object-cover aspect-square group-hover:scale-105 transition duration-300 ease-in-out"
                src="https://casasperanteisiabucuriei.ro/images/slideshow_index_image/1.jpg"

            />
        </div>
 </div>
    
  )
}

export default Menu