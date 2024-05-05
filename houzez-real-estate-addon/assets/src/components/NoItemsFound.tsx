import React from "react";

export default function NoItemsFound({ text }: NoItemsFoundProps) {
  return <div className="no-items-found py-6 text-center">{text}</div>;
}

export interface NoItemsFoundProps {
  text: string;
}
