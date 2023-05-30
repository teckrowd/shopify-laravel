import * as React from "react";
import notFoundImage from "../assets/empty-state.svg";
import { Page, Card, EmptyState } from "@shopify/polaris";

const NotFound = () => {
  return (
    <Page>
      <Card>
        <EmptyState
          heading="There is no page at this address"
          image={notFoundImage}
        >
          <p>
            Check the URL and try again, or use the search bar to find what you need.
          </p>
        </EmptyState>
      </Card>
    </Page>
  );
}

export default NotFound;