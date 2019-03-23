// @flow
import React from 'react';
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
    this.setState({ avatar: files[0] });
  };

  handleSubmit = () => {
    const formData = new FormData();
    formData.append('avatar', this.state.avatar);
    this.props.onSubmit(this.state.avatar).then(() => this.setState(initState));
  };

  render() {
    const MEGABYTE = 1048576;
    const FILESIZE_LIMIT = 2 * MEGABYTE;
    const { avatar } = this.state;
    return (
      <form className="form-horizontal">
        <div className="form-group" style={{ width: 200 }}>
          <ImageUploader
            fileContainerStyle={{ backgroundColor: '#18191d' }}
            labelClass="text-center"
            errorClass="text-center"
            withIcon={false}
            withPreview={false}
            buttonText="Vyber obrázek"
            fileSizeError="Maximální velikost obrázku jsou 2 MB."
            fileTypeError="Soubor musí být obrázek."
            label="Maximální velikost obrázku jsou 2 MB."
            onChange={this.handleChange}
            imgExtension={['.jpg', '.gif', '.png', '.gif']}
            maxFileSize={FILESIZE_LIMIT}
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
