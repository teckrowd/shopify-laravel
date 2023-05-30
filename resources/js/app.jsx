import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { NavigationMenu } from '@shopify/app-bridge-react';
import Routes from "./routes";
import AppBridgeProvider from './components/providers/appBridgeProvider';
import PolarisProvider from './components/providers/polarisProvider';
import QueryProvider from './components/providers/queryProvider';
import { FooterHelp, Link } from '@shopify/polaris';

const App = () => {
  // Any .tsx or .jsx files in /pages will become a route
  // See documentation for <Routes /> for more info
  const pages = import.meta.globEager("./pages/**/!(*.test.[jt]sx)*.([jt]sx)");

  return (
    <PolarisProvider>
      <BrowserRouter>
        <AppBridgeProvider>
          <QueryProvider>
            <NavigationMenu
              navigationLinks={[
                {
                  label: "Page name",
                  destination: "/shopify/pagename",
                },
              ]}
            />
            <Routes pages={pages} />
            <FooterHelp>
              <p>This template was built by <Link url="https://teckrowd.com" external monochrome>Teckrowd</Link>.</p>
            </FooterHelp>
          </QueryProvider>
        </AppBridgeProvider>
      </BrowserRouter>
    </PolarisProvider>
  );
} 

export default App;

document.addEventListener('DOMContentLoaded', function(event) {
  if(document.getElementById('root')) {
    createRoot(document.getElementById('root')).render(<App />);
  }
});