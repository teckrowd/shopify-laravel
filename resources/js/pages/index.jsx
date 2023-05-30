import * as React from 'react';
import { Layout, Page, Card, VerticalStack, Text, Link } from '@shopify/polaris';
import { TitleBar } from '@shopify/app-bridge-react';
import trophyImage from '../assets/home-trophy.png';
import ProductsCard from '../components/ProductsCard';

const Homepage = () => {
  return (
    <Page narrowWidth>
      <TitleBar title="App name"
        primaryAction={null} />
      <Layout>
        <Layout.Section>
          <Card>
            <VerticalStack gap="5">
              <Text variant="headingLg"
                as="h1">Nice work on building a Shopify app 🎉</Text>
              <p>
                Your app is ready to explore! It contains everything you need to get started including the{" "}
                <Link url="https://polaris.shopify.com/" external>Polaris design system</Link>,{" "}
                <Link url="https://shopify.dev/api/admin-graphql" 
                  external>Shopify Admin API</Link>, and{" "}
                <Link url="https://shopify.dev/apps/tools/app-bridge"
                  external>App Bridge</Link>{" "}
                UI library and components.
              </p>
              <p>
                Ready to go? Start populating your app with some sample products to view and test in your store.{" "}
              </p>
              <p>
                Learn more about building out your app in{" "}
                <Link url="https://shopify.dev/apps/getting-started/add-functionality"
                  external>this Shopify tutorial</Link>{" "}
                📚{" "}
              </p>
              <VerticalStack inlineAlign="center">
                <img src={trophyImage}
                  alt="Nice work on building a Shopify app" />
              </VerticalStack>
            </VerticalStack>
          </Card>
        </Layout.Section>
        <Layout.Section>
          <ProductsCard />
        </Layout.Section>
      </Layout>
    </Page>
  )
}

export default Homepage;