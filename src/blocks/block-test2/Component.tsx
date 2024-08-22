import { ComponentProps } from "@/@types/index.type";
import { RichText } from "@wordpress/block-editor";
import { useState } from "@wordpress/element";
import type { Attributes } from ".";

interface OtherProps {
	edit?: boolean;
}

export type ComponentPropsEdit = ComponentProps<Attributes, OtherProps, true>;

const Component: ComponentPropsEdit = (props) => {
	const { heading, headingColor, content, contentColor, image } =
		props.attributes;

	const RichTextComponent = props.edit ? RichText : RichText.Content;
	return (
		<div
			data-mt-attributes={JSON.stringify(props.attributes)}
			// {...(props.edit ? useBlockProps() : useBlockProps.save())}
		>
			<img className="feature-icon flex" src={image} />
			<RichTextComponent
				tagName="h5"
				value={heading}
				onChange={(newContent) => props.setAttributes({ heading: newContent })}
				style={{ color: headingColor }}
			/>
			<RichTextComponent
				tagName="p"
				value={content}
				onChange={(newContent) => props.setAttributes({ content: newContent })}
				style={{ color: contentColor }}
			/>
		</div>
	);
};

export default Component;

export const ComponentWrapper: ComponentProps<Attributes> = (props) => {
	const [count, setCount] = useState(0);

	return (
		<div
			className="mt-block-test2"
			data-mt-attributes={JSON.stringify(props.attributes)}
		>
			<button onClick={() => setCount(count + 1)}>{count}</button>
			<Component {...props} />
		</div>
	);
};
