import { Card, Layout, Page, Text, VerticalStack } from "@shopify/polaris";
import { TitleBar } from '@shopify/app-bridge-react';
import * as React from "react";

const Pagename = () => {
  return (
    <Page>
      <TitleBar title="Page name"
        primaryAction={{
          content: "Primary action",
          onAction: () => console.log("Primary action"),
        }}
        secondaryActions={[
          {
            content: "Secondary action",
            onAction: () => console.log("Secondary action")
          }
        ]} />
      <Layout>
        <Layout.Section>
          <Card>
            <VerticalStack gap="5">
              <VerticalStack gap="2">
                <Text variant="headingLg"
                  as="h1">Heading</Text>
                <p>Body</p>
              </VerticalStack>
            </VerticalStack>
          </Card>
        </Layout.Section>
      </Layout>
    </Page>
  )
}

export default Pagename;