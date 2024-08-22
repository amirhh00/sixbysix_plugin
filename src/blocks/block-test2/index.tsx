import { registerBlockType } from "@wordpress/blocks";
import metadata from "./block.json";
import Edit from "./edit";
import Component from "./Component";

export type Attributes = (typeof import("./block.json"))["attributes"];

const Save = (props: any) => (
	<div
		className="mt-block-test2-wrapper"
		data-mt-attributes={JSON.stringify(props.attributes)}
	>
		<div className="mt-block-test2">
			<Component {...props} />
		</div>
	</div>
);

registerBlockType(metadata as any, {
	edit: Edit,
	save: Save,
});
