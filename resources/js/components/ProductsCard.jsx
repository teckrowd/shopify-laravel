import * as React from 'react';
import { useAuthenticatedFetch } from '../hooks/useAuthenticatedFetch';
import { Toast } from "@shopify/app-bridge-react";
import { Button, Card, Text, VerticalStack } from '@shopify/polaris';

const ProductsCard = () => {
  const emptyToastProps = { content: null };
  const [isLoading, setIsLoading] = React.useState(true);
  const [toastProps, setToastProps] = React.useState(emptyToastProps);
  const [productCount, setProductCount] = React.useState(null);
  const fetch = useAuthenticatedFetch();

  const fetchProductCount = async () => {
    setIsLoading(true);
    const response = await fetch(`/shopify/products/count`)
      .then(response => response.json())
      .then(data => {
        setProductCount(data.count);
      })
      .catch(e => {
        console.error(e);
        setToastProps({
          content: "There was an error fetching product count.",
          error: true
        });
      })
    setIsLoading(false);
  }

  const createProducts = async () => {
    setIsLoading(true);
    const response = await fetch(`/shopify/products/create`)
      .then(response => response.json())
      .then(data => {
        setToastProps({
          content: "Products created."
        });
        setProductCount(productCount + 5);
      })
      .catch(e => {
        console.error(e);
        setToastProps({
          content: "There was an error fetching product count.",
          error: true
        });
      })
    setIsLoading(false);
  }

  React.useEffect(() => {
    fetchProductCount()
  }, []);

  return (
    <>
      {toastProps.content && (
        <Toast {...toastProps}
          onDismiss={() => setToastProps(emptyToastProps)} />
      )}
      <Card>
        <VerticalStack gap="5">
          <Text variant="headingLg"
            as="h3">Product Counter</Text>
          <p>Sample products are created with a default title and price. You can remove them at any time.</p>
          <div>
            <Text variant="headingLg"
              as="h3">TOTAL PRODUCTS: <Text as="span">{!productCount ? "-" : productCount }</Text></Text>
          </div>
          <div>
            <Button onClick={createProducts}
              fullWidth={false}
              disabled={isLoading}>Create 5 random products</Button>
          </div>
        </VerticalStack>
      </Card>
    </>
  )
}

export default ProductsCard;