import Head from 'next/head'
import Login from '../components/Login'
import Nav from '../components/Nav'
import Menu from '../components/Menu'

import { getSession } from 'next-auth/react'

export default function Home({ session }) {
  
  // if (!session) return <Login />;
  
  return (
    <div className='h-screen bg-gray-100 overflow-hidden'>
      <Head>
        <title>Mircea are nevoie de haine</title>
      </Head>

      <Nav />
      {/* <Header /> */}

      <div className='flex'>

        <Menu />
        {/* <Sidebar /> */}
        {/* <Feed /> */}
        {/* Widgets */}

      </div>

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