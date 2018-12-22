// @flow
import React from 'react';
import styled from 'styled-components';
import type { PostedBulletpointType } from '../theme/bulletpoint/types';

const SpaceLink = styled.a`
  margin-right: 5px;
`;

type ButtonState = 'default' | 'opened';

type SubmitButtonProps = {|
  state: ButtonState,
  onClick: () => (void),
  children: string,
|};
const SubmitButton = ({ state, onClick, children }: SubmitButtonProps) => {
  if (state === 'default') {
    return (
      <SpaceLink className="btn btn-default" onClick={onClick} role="button">{children}</SpaceLink>
    );
  } else if (state === 'opened') {
    return (
      <SpaceLink className="btn btn-success" onClick={onClick} role="button">{children}</SpaceLink>
    );
  }
  return null;
};

type CancelButtonProps = {|
  state: ButtonState,
  onClick: () => (void),
  children: string,
|};
const CancelButton = ({ state, onClick, children }: CancelButtonProps) => {
  if (state === 'opened') {
    return (
      <SpaceLink className="btn btn-danger" onClick={onClick} role="button">{children}</SpaceLink>
    );
  }
  return null;
};

type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};

type Props = {|
  +onSubmit: (PostedBulletpointType) => (void),
|};
type State = {|
  button: {|
    state: ButtonState,
  |},
  bulletpoint: PostedBulletpointType,
|};
const initState = {
  bulletpoint: {
    content: '',
    source: {
      link: '',
      type: 'web',
    },
  },
  button: { state: 'default' },
};
class Add extends React.Component<Props, State> {
  state = initState;

  onChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    if (name === 'source_link') {
      input = { source: { ...this.state.bulletpoint.source, link: value } };
    } else if (name === 'source_type') {
      input = { source: { type: value, link: '' } };
    } else {
      input = { ...this.state.bulletpoint, [name]: value };
    }
    this.setState(prevState => ({
      ...prevState,
      bulletpoint: {
        ...prevState.bulletpoint,
        ...input,
      },
    }));
  };

  onSubmit = () => {
    if (this.state.button.state === 'default') {
      this.setState(prevState => ({ ...prevState, button: { state: 'opened' } }));
    } else if (this.state.button.state === 'opened') {
      this.props.onSubmit(this.state.bulletpoint);
      this.setState(initState);
    }
  };

  render() {
    const { bulletpoint, button } = this.state;
    let form = null;
    if (button.state !== 'default') {
      form = (
        <form>
          <div className="form-group">
            <label htmlFor="content">Obsah</label>
            <input type="text" className="form-control" id="content" name="content" value={bulletpoint.content} onChange={this.onChange} />
          </div>
          <div className="form-group">
            <label htmlFor="source_type">Typ zdroje</label>
            <select className="form-control" id="source_type" name="source_type" value={bulletpoint.source.type} onChange={this.onChange}>
              <option value="web">Web</option>
              <option value="head">Z vlastní hlavy</option>
            </select>
            {
              bulletpoint.source.type === 'web'
                ? <>
                  <label htmlFor="source_link">Odkaz na zdroj</label>
                  <input type="text" className="form-control" id="source_link" name="source_link" value={bulletpoint.source.link} onChange={this.onChange} />
                </>
                : null
            }
          </div>
        </form>
      );
    }
    return (
      <>
        {form}
        <SubmitButton onClick={this.onSubmit} state={button.state}>
          Přidat bulletpoint
        </SubmitButton>
        <CancelButton onClick={() => this.setState(initState)} state={button.state}>
          Zrušit
        </CancelButton>
      </>
    );
  }
}

export default Add;
