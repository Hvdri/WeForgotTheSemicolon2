import Head from 'next/head'
import Login from '../components/Login'

import { getSession } from 'next-auth/react'

export default function Home({ session }) {
  
  if (!session) return <Login />;
  
  return (
    <div className='h-screen bg-gray-100 overflow-hidden'>
      <Head>
        <title>HoriaBook</title>
      </Head>

      {/* <Header /> */}

      <main className='flex'>

        {/* <Sidebar /> */}
        {/* <Feed /> */}
        {/* Widgets */}

      </main>

    </div>
  )
}

export async function getServerSideProps(context) {
  // Get the user
  const session = await getSession(context);

  return {
    props: {
      // changed 
      session: await getSession(context),
    },
  }

}