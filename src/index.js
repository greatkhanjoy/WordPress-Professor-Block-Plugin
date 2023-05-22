import React, { useState, useEffect } from "react";
import apiFetch from "@wordpress/api-fetch";
import { useSelect } from "@wordpress/data";
import { useBlockProps } from "@wordpress/block-editor";
import "./index.scss";
const __ = wp.i18n.__;

wp.blocks.registerBlockType("greatkhanjoy/professor", {
  title: "Professor",
  description: __("Block to generate a professor", "professor-greatkhanjoy"),
  icon: "welcome-learn-more",
  category: "common",
  attributes: {
    profId: { type: "string" },
  },
  edit: EditComponent,
  save: function (props) {
    return null;
  },
});

function EditComponent(props) {
  const [thePreview, setThePreview] = useState("");

  useEffect(() => {
    if (props.attributes.profId) {
      apiFetch({
        path: `/professor/v1/getHTML?profId=${props.attributes.profId}`,
        method: "GET",
      }).then((prof) => {
        setThePreview(prof);
      });
    }
  }, [props.attributes.profId]);

  const allProfs = useSelect((select) => {
    return select("core").getEntityRecords("postType", "professor", {
      per_page: -1,
    });
  });

  if (allProfs === undefined)
    return `<p>${__("Loading...", "professor-greatkhanjoy")}</p>`;

  const blockProps = useBlockProps({
    className: "flex flex-col",
  });

  return (
    <div {...blockProps}>
      <div className="professor_selector bg-gray-400 px-2 py-4 ">
        <select
          onChange={(e) => props.setAttributes({ profId: e.target.value })}
          className="w-full border border-gray-300 rounded-md"
        >
          <option>{__("Select a professor", "professor-greatkhanjoy")}</option>
          {allProfs?.map((prof) => {
            return (
              <option
                value={prof.id}
                selected={props.attributes.profId == prof.id}
              >
                {prof.title.rendered}
              </option>
            );
          })}
        </select>
      </div>
      <div dangerouslySetInnerHTML={{ __html: thePreview }}></div>
    </div>
  );
}
