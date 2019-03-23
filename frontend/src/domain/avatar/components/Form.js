// @flow
import React from 'react';
import { last } from 'lodash';
import ImageUploader from 'react-images-upload';

type TargetType = {|
  target: {|
    name: string,
    files: Array<*>,
  |},
|};

const initState = {
  avatar: '',
};
type Props = {|
  +onSubmit: (FormData) => (void),
|};
type State = {|
  avatar: string,
|};
class Form extends React.Component<Props, State> {
  state = initState;

  handleChange = (files: Array<File>) => {
    this.setState({ avatar: last(files) });
  };

  handleSubmit = () => {
    const formData = new FormData();
    formData.append('avatar', this.state.avatar);
    this.props.onSubmit(this.state.avatar).then(() => this.setState(initState));
  };

  render() {
    const MEGABYTE = 1048576;
    const MEGABYTE_LIMIT = 2;
    const BYTES_LIMIT = MEGABYTE_LIMIT * MEGABYTE;
    const { avatar } = this.state;
    return (
      <form className="form-horizontal">
        <div className="form-group" style={{ width: 160 }}>
          <ImageUploader
            name="avatar"
            singleImage
            fileContainerStyle={{ backgroundColor: '#18191d' }}
            labelClass="text-center"
            errorClass="text-center"
            withIcon={false}
            withPreview={false}
            buttonText="Vyber obrázek"
            fileSizeError={`Maximální velikost obrázku jsou ${MEGABYTE_LIMIT} MB.`}
            fileTypeError="Soubor musí být obrázek."
            label={`Maximální velikost obrázku jsou ${MEGABYTE_LIMIT} MB.`}
            onChange={this.handleChange}
            imgExtension={['.jpg', '.gif', '.png', '.gif']}
            maxFileSize={BYTES_LIMIT}
          />
        </div>
        {avatar && (
          <div className="form-group">
            <button type="button" onClick={this.handleSubmit} className="btn btn-success">
              Nahrát
            </button>
          </div>)}
      </form>
    );
  }
}

export default Form;
